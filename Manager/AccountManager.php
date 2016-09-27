<?php

namespace WobbleCode\BillingBundle\Manager;

use MongoDB\BSON\UTCDatetime;
use WobbleCode\BillingBundle\Document\Account;
use WobbleCode\BillingBundle\Document\AccountStatement;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use WobbleCode\ManagerBundle\Manager\GenericDocumentManager;
use WobbleCode\UserBundle\Model\OrganizationInterface;

class AccountManager extends GenericDocumentManager
{
    /**
     * Get available
     *
     * @param OrganizationInterface $organization Orgnization accout owner
     * @param String       $type         Type name of the account Eg: marketing
     *
     * @return float
     */
    public function getAvailable($organization, $type)
    {
        $account = $this->findAccountByType($organization, $type);

        return $account->getAvailable();
    }

    /**
     * Find organization account by type or creates a new one if doesn't exists.
     *
     * @param OrganizationInterface $organization Orgnization accout owner
     * @param String       $type         Type name of the account Eg: marketing
     *
     * @return Account Found account or new created
     */
    public function findAccountByType($organization, $type)
    {
        $account = $this->dm
                        ->getRepository('WobbleCodeBillingBundle:Account')
                        ->findOneBy(array(
                            'organization.$id' => new \MongoId($organization->getId()),
                            'type' => $type
                        ));

        if (!$account) {
            $account = $this->createNewAccount($type, 'EUR');
        }

        return $account;
    }

    /**
     * Create a new account for an Organization if type doesn't exists already
     *
     * @param String       $type         Type name of the account Eg: marketing
     * @param String       $currency     ISO Currency 3 letters Eg: EUR
     *
     * @return Account new account created
     */
    public function createNewAccount($type, $organization, $currency = 'EUR')
    {
        $account = new Account;
        $account->setType($type);
        $account->setCurrency($currency);

        $organization->setAccount($account);
        $this->dm->persist($organization);
        $this->dm->flush();

        return $account;
    }

    /**
     * Creates and set a new statement, it wils refresh Account as well
     *
     * @param Account  $account      Acccount where the statement will be added
     * @param String   $type         Name type of the statemente
     * @param String   $title        Title message
     * @param String   $desc         Detailed description of the statement
     * @param DateTime $effectiveAt  Date at statment will be effective
     * @param Float    $amount       Amount to add negative or positive
     * @param String   $hash         A sha1 hash to prevent duplicates
     *
     * @return AccountStatement Account statement created in the process
     */
    public function setStatement(
        Account $account,
        $type,
        $title,
        $description,
        $effectiveAt,
        $amount,
        $hash
    ) {

        $accountStatement = new AccountStatement;
        $accountStatement->setTitle($title)
            ->setDescription($description)
            ->setAmount($amount)
            ->setHash($hash)
            ->setType($type)
            ->setAccount($account)
            ->setEffectiveAt($effectiveAt);

        $account->addStatement($accountStatement);

        $this->dm->persist($account);
        $this->dm->persist($accountStatement);
        $this->dm->flush();

        $this->refresh($account);

        return $accountStatement;
    }

    public function makeEffective($accountStatement)
    {
        $accountStatement->setEffectiveAt(new \DateTime('now'));

        $this->dm->persist($accountStatement);
        $this->dm->flush();
    }

    public function charge($organization, $amount)
    {
        $confirm = $this->checkCreditForOperation($organization, $amount);

        if ($confirm) {
            return $this->modifyBalance($organization, $amount);
        }

        return false;
    }

    public function checkCreditForOperation(OrganizationInterface $organization, $amount)
    {
        $this->dm->refresh($organization);
        $account = $organization->getAccount();
        $available = $account->getAvailable() + $organization->getOrganizationSetting()->getPostPaidAmount();

        if ($available + $amount < 0) {
            return false;
        }

        return true;
    }

    /**
     * Modify the balance of an account
     *
     * @param Integer $id Account Statement id
     *
     * @return void
     */
    public function modifyBalance($organization, $amount, $accountProperty = 'account', $precision = 4)
    {
        $collection = $this->dm->getDocumentCollection(get_class($organization));

        $criteria = [
            '_id' => new \MongoId($organization->getId())
        ];

        $query = [
            '$inc' => [$accountProperty.'.available.value' => intval($amount* 10**$precision)]
        ];

        if ($organization->getAccount()->getDebt() < 0) {
            $query['$inc'] = [
                $accountProperty.'.debt.value' => intval($amount* 10**$precision),
                $accountProperty.'.available.value' => intval($amount* 10**$precision)
            ];
        }

        if ($amount > 0) {
            $query['$set'] = [
                $accountProperty.'.attributes.notificationsLowCredit' => [],
                $accountProperty.'.lastPositiveInput.value'           => intval($amount* 10**$precision),
                $accountProperty.'.lastPositiveInput.precision'       => intval($precision),
                $accountProperty.'.lastPositiveInput.createdAt'       => new \MongoDate()
            ];
        }

        $result = $collection->update($criteria, $query);
        $this->dm->refresh($organization);

        return $result;
    }

    /**
     * Remove an statement by Id
     *
     * @param Integer $id Account Statement id
     *
     * @return void
     */
    public function removeStatement($id)
    {
        $accountStatement = $this->dm->getRepository('WobbleCodeBillingBundle:AccountStatement')->find($id);

        $this->dm->remove($accountStatement);
        $this->dm->flush();
    }

    /**
     * Get
     *
     * @param integer $percent Search by percent Ej: 30 for 30%
     *
     * @return OrganizationInterface[]
     */
    public function getAccountsLowBalanceByPercent($percent, $period)
    {
        $date = new \DateTime();
        $date->modify($period);
        $date = $this->normalizeDateToMongo($date);
        $part = $percent / 100;

        $qb = $this->dm->createQueryBuilder($this->document);
        $accounts = $qb
            ->find()
            ->field('account.lastPositiveInput.value')->gt(0)
            ->field('account.available.value')->gte(0)
            ->addOr($qb->expr()->field('account.attributes.notificationsLowCredit.percent_'.$percent)->equals(null))
            ->addOr($qb->expr()->field('account.attributes.notificationsLowCredit.percent_'.$percent)->lt($date))
            ->where('this.account.available.value < this.account.lastPositiveInput.value * '.$part)
            ->getQuery()
            ->execute();

        return $accounts;
    }

    /**
     * Get organizations with negative account
     *
     * @return OrganizationInterface[]
     */
    public function getAccountsNegativeBalance()
    {
        $qb = $this->dm->createQueryBuilder($this->document);
        $organizations = $qb
            ->find()
            ->field('account.available.value')->lt(0)
            ->getQuery()
            ->execute();

        return $organizations;
    }

    /**
     * Get organizations with negative account
     *
     * @param array $invoiceDays
     *
     * @return OrganizationInterface[]
     */
    public function getOrganizationsToGenerateInvoice(array $invoiceDays)
    {
        $qb = $this->dm->createQueryBuilder($this->document);
        $organizations = $qb
            ->find()
            ->field('organizationSetting.invoiceDays')->in($invoiceDays)
            ->field('organizationSetting.postPaidAmount')->gt(0)
            ->where('(this.account.available.value - this.account.debt.value) < 0')
            ->getQuery()
            ->execute();

        return $organizations;
    }

    public function setNotificationKey(OrganizationInterface $organization, $key)
    {
        if (!$organization->getAccount()->getAttributes()) {
            $organization->getAccount()->setAttributes([]);
            $this->save([$organization]);
        }

        $this->dm->createQueryBuilder($this->document)
            ->update()
            ->field('id')->equals(new \MongoId($organization->getId()))
            ->field('account.attributes.'.$key)->set(new \MongoDate())
            ->getQuery()
            ->execute();
    }
}

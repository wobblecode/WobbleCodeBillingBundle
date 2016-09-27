<?php

namespace WobbleCode\BillingBundle\Manager;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use WobbleCode\BillingBundle\Document\Account;
use WobbleCode\BillingBundle\Document\Invoice;
use WobbleCode\BillingBundle\Document\InvoiceStatement;
use WobbleCode\BillingBundle\Document\AccountStatement;
use WobbleCode\BillingBundle\Document\ChargeRequest;
use WobbleCode\BillingBundle\Manager\InvoiceManager;
use WobbleCode\BillingBundle\Manager\InvoiceProfileManager;
use WobbleCode\UserBundle\Manager\OrganizationManager;
use WobbleCode\UserBundle\Model\OrganizationInterface;
use WobbleCode\ManagerBundle\Manager\GenericDocumentManager;
use WobbleCode\BillingBundle\Exception\InvalidStatusException;

class ChargeRequestManager extends GenericDocumentManager
{
    /**
     * InvoiceManager
     *
     * @var InvoiceManager
     */
    private $invoiceManager;

    /**
     * AccountManager
     *
     * @var AccountManager
     */
    private $accountManager;

    /**
     * OrganizationManager
     *
     * @var OrganizationManager
     */
    private $organizationManager;

    /**
     * Set AccountManager
     *
     * @param AccountManager $accountManager
     */
    public function setAccountManager(AccountManager $accountManager)
    {
        $this->accountManager = $accountManager;
    }

    /**
     * Set invoice Manager
     *
     * @param InvoiceManager $invoiceManager
     */
    public function setInvoiceManager(InvoiceManager $invoiceManager)
    {
        $this->invoiceManager = $invoiceManager;
    }

    /**
     * Set invoice Manager
     *
     * @param InvoiceManager $invoiceManager
     */
    public function setInvoiceProfileManager(InvoiceProfileManager $invoiceProfileManager)
    {
        $this->invoiceProfileManager = $invoiceProfileManager;
    }

    /**
     * Set OrganizationManager
     *
     * @param InvoiceManager $invoiceManager
     */
    public function setOrganizationManager(OrganizationManager $organizationManager)
    {
        $this->organizationManager = $organizationManager;
    }

    /**
     * Count charge request by status
     *
     * @param string $status
     *
     * @return int
     */
    public function countByStatus($status, $organization = null)
    {
        $match = [
            [
                'field' => 'status',
                'selector' => 'equals',
                'value' => $status
            ]
        ];

        if ($organization) {
            $match[] = [
                'field' => 'organization.id',
                'selector' => 'equals',
                'value' => new \MongoId($organization->getId())
            ];
        }

        return $this->count($match, false);
    }

    /**
     * End user has give his confirm for this charge Request
     *
     * @param ChargeRequest $chargeRequest ChargeRequest Object
     */
    public function confirm(ChargeRequest $chargeRequest)
    {
        if (!$chargeRequest->isConfirmable()) {
            throw new InvalidStatusException(
                'The current status "'.$chargeRequest->getStatus().'" doesn\'t allow to be confirmed.'
            );
        }

        $chargeRequest->setStatus('confirmed');
        $chargeRequest->setConfirmedAt(new \DateTime('now'));

        $this->dm->persist($chargeRequest);
        $this->dm->flush();

        $organization = $chargeRequest->getOrganization();

        $this->eventDispatcher->dispatch(
            'billing.chargeRequest.confirmed',
            new GenericEvent('billing.chargeRequest.confirmed', [
                'notifyOrganizations' => [$organization],
                'notifyOrganizationTrigger' => $organization,
                'data' => [
                    'chargeRequest' => $chargeRequest
                ]
            ])
        );
    }

    public function execute(ChargeRequest $chargeRequest)
    {
        if (!$chargeRequest->isExecutable()) {
            throw new InvalidStatusException(
                'The current status "'.$chargeRequest->getStatus().'" doesn\'t allow to be executed.'
            );
        }

        $oldStatus = $chargeRequest->getStatus();
        $amount = $chargeRequest->getAmount();
        $organization = $chargeRequest->getOrganization();
        $this->accountManager->modifyBalance($organization, $amount);

        $chargeRequest->setStatus('executed');
        $chargeRequest->setExecutedAt(new \DateTime('now'));
        $this->dm->persist($chargeRequest);
        $this->dm->flush();

        if ($oldStatus == 'pending') {
            $invoice = $chargeRequest->getInvoice();
            $invoice->setPaymentProfile($chargeRequest->getPaymentProfile());
            $this->invoiceManager->save([$invoice]);
        } else {
            $invoice = $this->createInvoice($chargeRequest);
        }

        $this->eventDispatcher->dispatch(
            'billing.chargeRequest.executed',
            new GenericEvent('billing.chargeRequest.executed', [
                'notifyOrganizations' => [$organization],
                'notifyOrganizationTrigger' => $organization,
                'data' => [
                    'organization' => $organization,
                    'chargeRequest' => $chargeRequest,
                    'invoice' => $invoice
                ]
            ])
        );
    }

    public function cancel(ChargeRequest $chargeRequest)
    {
        if (!$chargeRequest->isCancelable()) {
            throw new InvalidStatusException(
                'The current status "'.$chargeRequest->getStatus().'" doesn\'t allow to be cancelable.'
            );
        }

        $chargeRequest->setStatus('canceled');
        $chargeRequest->setCanceledAt(new \DateTime('now'));

        $this->dm->persist($chargeRequest);
        $this->dm->flush();

        $organization = $chargeRequest->getOrganization();

        $this->eventDispatcher->dispatch(
            'billing.chargeRequest.canceled',
            new GenericEvent('billing.chargeRequest.canceled', [
                'notifyLevel' => 'error',
                'notifyOrganizations' => [$organization],
                'notifyOrganizationTrigger' => $organization,
                'data' => [
                    'chargeRequest' => $chargeRequest
                ]
            ])
        );
    }

    /**
     * Create invoice from charge request
     */
    public function createInvoice(
        ChargeRequest $chargeRequest
    ) {
        $issuerOrganization = $this->organizationManager->getAdminOwner();
        $issuerContact = $issuerOrganization->getContact();

        $recipientOrganization = $chargeRequest->getOrganization();
        $recipientContact = $recipientOrganization->getContact();

        $invoiceProfile = $issuerOrganization->getInvoiceProfile();
        $ref = $invoiceProfile->getRenderedRef();

        $invoice = new Invoice();
        $invoice->setIssuer($issuerContact);
        $invoice->setType('received');
        $invoice->setRecipient($recipientContact);
        $invoice->setChargeRequest($chargeRequest);

        if ($chargeRequest->getPaymentProfile()) {
            $invoice->setPaymentProfile($chargeRequest->getPaymentProfile());
        }

        $invoice->setOrganization($recipientOrganization);
        $invoice->setHash(sha1($chargeRequest->getId()));
        $invoice->setReference($ref);
        $invoice->setStatus('open');

        $invoiceStatement = new InvoiceStatement;
        $invoiceStatement->setType('unitary');
        $invoiceStatement->setTitle($chargeRequest->getChargeTitle());
        $invoiceStatement->setAmount($chargeRequest->getAmount());
        $invoice->addStatement($invoiceStatement);

        if ($chargeRequest->getFeePercentage() > 0) {
            $invoiceFeeStatement = new InvoiceStatement;
            $invoiceFeeStatement->setType('general');
            $invoiceFeeStatement->setTitle($chargeRequest->getFeeTitle());
            $invoiceFeeStatement->setAmount($chargeRequest->getFeePercentage());
            $invoice->addStatement($invoiceFeeStatement);
        }

        if ($chargeRequest->getTaxPercentage() > 0) {
            $chargeTaxRequest = new InvoiceStatement;
            $chargeTaxRequest->setType('taxes');
            $chargeTaxRequest->setTitle($chargeRequest->getTaxTitle());
            $chargeTaxRequest->setAmount($chargeRequest->getTaxPercentage());
            $invoice->addStatement($chargeTaxRequest);
        }

        if ($chargeRequest->getStatus() == 'pending') {
            $date = new \DateTime();
            $date->modify("+".$recipientOrganization->getOrganizationSetting()->getInvoiceDueDays()." days");
            $invoice->setDueAt($date);
        }

        $chargeRequest->setInvoice($invoice);

        $this->dm->persist($invoice);
        $this->invoiceProfileManager->incrementRef($invoiceProfile);

        $this->dm->persist($invoiceStatement);
        $this->dm->persist($chargeRequest);
        $this->dm->flush();

        $this->invoiceManager->close($invoice);

        return $invoice;
    }
}

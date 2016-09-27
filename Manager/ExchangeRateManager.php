<?php

namespace WobbleCode\BillingBundle\Manager;

use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\GenericEvent;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ODM\MongoDB\DocumentManager;
use WobbleCode\UserBundle\Model\OrganizationInterface;
use WobbleCode\BillingBundle\Document\Invoice;
use WobbleCode\BillingBundle\Document\ChargeRequest;
use WobbleCode\BillingBundle\Document\InvoiceStatement;
use WobbleCode\ManagerBundle\Manager\GenericDocumentManager;

class ExchangeRateManager extends GenericDocumentManager
{
    /**
     * Update ratios from a currency to a multiple currencies and save them into
     * the database, with a defined time point that determines when the exchange
     * starts to be effective.
     *
     * @param String    $from ISO 3 Letter Currency for base conversion
     * @param Array     $to   Array of ISO Currencies {"currency": "ratio"}
     * @param \DateTime $date Time point when this ratio is effective
     *
     * @return self
     */
    public function updateRates($from, Array $to, \DateTime $date)
    {
        foreach ($to as $finalCurrency => $ratio) {
            $entity = new CurrencyExchanges();
            $entity->setBaseCurrency($from);
            $entity->setFinalCurrency($finalCurrency);
            $entity->setRatio($ratio);
            $entity->setDate($date);

            $this->dm->persist($entity);
        }

        $this->dm->flush();

        return $this;
    }
}

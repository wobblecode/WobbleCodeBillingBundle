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

class PaymentProfileManager extends GenericDocumentManager
{
    /**
     * Get enabled payment profiles for the system
     */
    public function getSystemActiveProfiles()
    {
        // TODO find system Organization

        return $this->findBy(
            array(
                'organization' => 1,
                'enabled' => true,
                'system' => true
            )
        );
    }

    public function getSystemProfileById($id)
    {
        return $this->findOneBy(
            array(
                'id' => $id,
                'organization' => 1,
                'enabled' => true,
                'system' => true
            )
        );
    }
}

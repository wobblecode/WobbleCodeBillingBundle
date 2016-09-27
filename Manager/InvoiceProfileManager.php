<?php

namespace WobbleCode\BillingBundle\Manager;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\GenericEvent;
use WobbleCode\ManagerBundle\Manager\GenericDocumentManager;
use WobbleCode\BillingBundle\Document\InvoiceProfile;

class InvoiceProfileManager extends GenericDocumentManager
{
    /**
     * InvoiceProfile
     *
     * @param InvoiceProfile $invoiceProfile
     *
     * @return seft
     */
    public function incrementRef($invoiceProfile)
    {
        $this->dm->createQueryBuilder('WobbleCodeBillingBundle:InvoiceProfile')
                       ->update()
                       ->field('id')->equals(new \MongoId($invoiceProfile->getId()))
                       ->field('lastIncrementAt')->set(new \MongoDate())
                       ->field('refId')->inc(1)
                       ->getQuery()
                       ->execute();

        return $this;
    }

    public function resetRefs()
    {
        $this->dm->createQueryBuilder('WobbleCodeBillingBundle:InvoiceProfile')
                       ->update()
                       ->field('lastIncrementAt')->set(null)
                       ->field('refId')->set(1)
                       ->getQuery()
                       ->execute();
    }
}

<?php

namespace WobbleCode\BillingBundle\Manager;

use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\GenericEvent;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ODM\MongoDB\DocumentManager;
use WobbleCode\UserBundle\Model\OrganizationInterface;
use WobbleCode\BillingBundle\Document\Invoice;
use WobbleCode\BillingBundle\Document\ChargeRequest;
use WobbleCode\BillingBundle\Document\InvoiceStatement;
use WobbleCode\ManagerBundle\Manager\GenericDocumentManager;

class InvoiceManager extends GenericDocumentManager
{
    /**
     * SnappyPdf
     *
     * @var SnappyPdf
     */
    protected $snappyPdf;

    /**
     * Router
     *
     * @var Router
     */
    protected $router;

    /**
     * Pdf Path
     *
     * @var string
     */
    protected $pdfPath;

    /**
     * File System
     *
     * @var string
     */
    protected $fileSystem;

    /**
     * Gaufrette
     *
     * @var Gaufrette
     */
    protected $gaufrette;

    /**
     * Set file system name
     *
     * @param string $fileSystem
     */
    public function setFileSystem($fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Set Pdf Path
     *
     * @param string $pdfPath
     */
    public function setPdfPath($pdfPath)
    {
        $this->pdfPath = $pdfPath;
    }

    /**
     * Set Router
     *
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Set SnappyPdf
     *
     * @param SnappyPdf $snappyPdf
     */
    public function setSnappyPdf($snappyPdf)
    {
        $this->snappyPdf = $snappyPdf;
    }

    /**
     * Set Router
     *
     * @param Router $router
     */
    public function setGaufrette($gaufrette)
    {
        $this->gaufrette = $gaufrette;
    }

    /**
     * The invoice is set to "ready" status
     *
     * @return void
     */
    public function ready(Invoice $invoice)
    {
        $invoice->setStatus('ready');
        $this->save([$invoice]);

        $this->eventDispatcher->dispatch(
            'billing.invoice.ready',
            new GenericEvent('billing.invoice.ready', array(
                'notifyOrganizationTrigger' => $invoice->getOrganization(),
                'notifyOrganizations'       => [$invoice->getOrganization()],
                'data'                      => array('invoice' => $invoice)
            ))
        );
    }

    /**
     * The invoice is set to "paid" status
     *
     * @return void
     */
    public function pay(Invoice $invoice)
    {
        $invoice->setStatus('paid');
        $this->save([$invoice]);

        $this->eventDispatcher->dispatch(
            'billing.invoice.paid',
            new GenericEvent('billing.invoice.paid', array(
                'notifyOrganizationTrigger' => $invoice->getOrganization(),
                'notifyOrganizations'       => [$invoice->getOrganization()],
                'data'                      => array('invoice' => $invoice)
            ))
        );
    }

    /**
     * The invoice is set to "rejected" invoice
     *
     * @return void
     */
    public function reject(Invoice $invoice)
    {
        $invoice->setStatus('rejected');
        $this->save([$invoice]);

        $this->eventDispatcher->dispatch(
            'billing.invoice.rejected',
            new GenericEvent('billing.invoice.rejected', array(
                'notifyOrganizationTrigger' => $invoice->getOrganization(),
                'notifyOrganizations'       => [$invoice->getOrganization()],
                'data'                      => array('invoice' => $invoice)
            ))
        );
    }

    /**
     * The invoice is set to "canceled" invoice
     *
     * @return void
     */
    public function cancel(Invoice $invoice)
    {
        $invoice->setStatus('canceled');
        $this->save([$invoice]);

        $this->eventDispatcher->dispatch(
            'billing.invoice.canceled',
            new GenericEvent('billing.invoice.canceled', array(
                'notifyOrganizationTrigger' => $invoice->getOrganization(),
                'notifyOrganizations'       => [$invoice->getOrganization()],
                'data'                      => array('invoice' => $invoice)
            ))
        );
    }

    /**
     * The invoice is set to "close" and creates the PDF file
     *
     * @return void
     */
    public function close(Invoice $invoice)
    {
        $invoice->setStatus('close');
        $this->save([$invoice]);

        $this->eventDispatcher->dispatch(
            'billing.invoice.close',
            new GenericEvent('billing.invoice.closed', array(
                'notifyOrganizationTrigger' => $invoice->getOrganization(),
                'notifyOrganizations'       => [$invoice->getOrganization()],
                'data'                      => array('invoice' => $invoice)
            ))
        );
    }

    /**
     * Create PDF file,
     *
     * TODO use Garuffete to manage files
     *
     * @param Invoice $invoice
     * @param string  $locale
     *
     * @return String Path or Id of the invoice
     */
    public function createPdf(Invoice $invoice, $locale = 'en')
    {
        $url = $this->router->generate('app_user_billing_invoice_show_open', ['id' => $invoice->getId()]);

        $schema = $this->router->getContext()->getScheme();
        $host = $this->router->getContext()->getHost();
        $port = $this->router->getContext()->getHttpPort();
        $url = $schema.'://'.$host.$url.'?_switch_lang='.$locale;

        // TODO Set Grauffete
        // see https://github.com/KnpLabs/snappy
        $path = $this->pdfPath.'/'.$invoice->getId().'.pdf';
        $this->snappyPdf->setOption('lowquality', null);
        $this->snappyPdf->generate($url, $path);

        $this->eventDispatcher->dispatch(
            'billing.invoice.pdfCreated',
            new GenericEvent('billing.invoice.pdfCreated', array(
                'notifyOrganizationTrigger' => $invoice->getOrganization(),
                'notifyOrganizations'       => [$invoice->getOrganization()],
                'data'                      => array('invoice' => $invoice)
            ))
        );
    }

    /**
     * Get / Create a pdf file if not exists and create an http response.
     *
     * @param Invoice $invoice
     * @param string  $locale
     *
     * @return Response
     */
    public function responsePdf(Invoice $invoice, $locale = 'en')
    {
        $path = $this->pdfPath.'/'.$invoice->getId().'.pdf';

        // TODO Set Grauffete
        // see https://github.com/KnpLabs/snappy
        if (file_exists($path) === false) {
            $this->createPdf($invoice, $locale);
        }

        $response = new Response();
        $response->headers->set('Content-type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s.pdf"', $invoice->getReference()));
        $response->setContent(file_get_contents($path));

        return $response;
    }

    public function getInvoicesByDueDate(\DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->dm->createQueryBuilder($this->document);
        $qb->field('dueAt')->gte($this->normalizeDateToMongo($startDate))
           ->field('dueAt')->lte($this->normalizeDateToMongo($endDate));

        return $qb->getQuery()->execute();
    }
}

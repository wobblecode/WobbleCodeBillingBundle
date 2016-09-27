<?php

namespace WobbleCode\BillingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;
use WobbleCode\BillingBundle\Document\Account;
use WobbleCode\BillingBundle\Document\ChargeRequest;

/**
 * Create a command line to check the current credit of organizations
 */
class InvoiceGeneratorCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('billing:invoice-generator')
            ->setDescription(
                'Generate Invoices for postpaid users'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accountManager = $this->getContainer()->get('wobblecode_billing.account_manager');
        $chargeRequestManager = $this->getContainer()->get('charge_request_manager');
        $invoiceDays = [];

        $today = new \DateTime();

        if ($today->format("N") == 1) {
            $invoiceDays[] = 7;
        }

        if ($today->format("d") == 15) {
            $invoiceDays[] = 15;
        }

        if ($today->format("d") == $today->format("t")) {
            $invoiceDays[] = 15;
            $invoiceDays[] = 30;
        }

        if ($today->format("d") == $today->format("t") && ($today->format("n") % 2 == 0)) {
            $invoiceDays[] = 60;
        }

        $organizations = $accountManager->getOrganizationsToGenerateInvoice($invoiceDays);

        foreach ($organizations as $organization) {
            $debtAmount = $organization->getAccount()->getDebt();
            $chargeRequest = new ChargeRequest();
            $chargeRequest->setOrganization($organization);
            $chargeRequest->setAmount(abs($organization->getAccount()->getAvailable() - $debtAmount));
            $chargeRequest->setStatus('pending');
            $organization->getAccount()->setDebt($organization->getAccount()->getAvailable());

            $chargeRequestManager->setTaxes($chargeRequest);
            $accountManager->save([$chargeRequest]);
            $accountManager->save([$organization->getAccount()]);

            $invoice = $chargeRequestManager->createInvoice($chargeRequest);

            $event = [
                'notifyOrganizations' => [$organization],
                'data' => [
                    'invoice' => $invoice,
                    'organization' => $organization,
                ],
            ];

            $accountManager->setNotificationKey($organization, 'invoice');
            $accountManager->dispatch('billing.account.postpaid.invoice', $event);

            $output->writeln(sprintf(
                '<info>Notify postpaid by %s</info> Current: %f €',
                $organization->getContactName(),
                $organization->getAccount()->getAvailable()
            ));
        }

    }
}

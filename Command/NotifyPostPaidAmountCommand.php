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
class NotifyPostPaidAmountCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('billing:notify-post-paid-amount')
            ->setDescription(
                'Reminder for pay bill x days before due date'
            )->addArgument(
                'days',
                InputArgument::REQUIRED,
                'Days before notify'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days = $input->getArgument('days');

        $startDate = new \DateTime();
        $startDate->modify("+".$days." days");
        $startDate->setTime(0, 0, 0);

        $endDate = clone $startDate;
        $endDate->setTime(23, 59, 59);

        $invoiceManager = $this->getContainer()->get('wobblecode_billing.invoice_manager');
        $invoices = $invoiceManager->getInvoicesByDueDate($startDate, $endDate);

        foreach ($invoices as $invoice) {

            if ($invoice->getChargeRequest()->getStatus() != "pending") {
                continue;
            }

            $event = [
                'notifyOrganizations' => [$invoice->getOrganization()],
                'data' => [
                    'invoice' => $invoice,
                ],
            ];

            $invoiceManager->dispatch('billing.account.postpaid.reminder', $event);

            $output->writeln(sprintf(
                '<info>Notify postpaid by %s</info> Amount: %f €',
                $invoice->getOrganization()->getContactName(),
                $invoice->getChargeRequest()->getAmount()
            ));
        }

    }
}

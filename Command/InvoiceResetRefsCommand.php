<?php

namespace WobbleCode\BillingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create a command line
 */
class InvoiceResetRefsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('invoice-profile:reset-refs')
            ->setDescription('Reset all ref IDs to 1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $profileManager = $this->getContainer()->get('wobblecode_billing.invoice_profile_manager');
        $profileManager->resetRefs();

        $output->writeln('All profiles reset');
    }
}

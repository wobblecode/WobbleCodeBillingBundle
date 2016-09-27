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
class InvoicePdfCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('invoice-pdf:create')
            ->setDescription('Create an invoice in pdf')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Id of the invoice'
            )
            ->addArgument(
                'locale',
                InputArgument::OPTIONAL,
                'Invoice lang'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');

        $locale = null;
        if ($input->getArgument('locale')) {
            $locale = $input->getArgument('locale');
        }

        $pdfCreator = $this->getContainer()->get('wobblecode_billing.invoice_manager');
        $pdfCreator->createPdf($id, $locale);

        $output->writeln('The file '.$id.'.pdf has been created');
    }
}

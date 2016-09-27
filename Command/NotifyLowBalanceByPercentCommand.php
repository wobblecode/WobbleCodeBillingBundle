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

/**
 * Create a command line to check the current credit of organizations
 */
class NotifyLowBalanceByPercentCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('billing:notify-low-balanace-by-percent')
            ->setDescription(
                'Dispatchs an event for accounts with low balance based on percent since last positive input'
            )
            ->addArgument(
                'percent',
                InputArgument::OPTIONAL,
                'Notify if lower than ? percent. 15 by default. Only intergers'
            )
            ->addArgument(
                'period',
                InputArgument::OPTIONAL,
                'Relative datetime: ex -1 month'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $percent = $input->getArgument('percent');
        $period = $input->getArgument('period');

        if (!$percent) {
            $percent = 15;
        }

        if (!$period) {
            $period = "-1 month";
        }

        $accountManager = $this->getContainer()->get('wobblecode_billing.account_manager');
        $organizations = $accountManager->getAccountsLowBalanceByPercent($percent, $period);

        if ($organizations == null) {
            return 1;
        }

        foreach ($organizations as $organization) {
            $event = [
                'notifyOrganizations' => [$organization],
                'data' => [
                    'mode' => 'percent',
                    'percent' => $percent,
                    'organization' => $organization,
                ],
            ];

            $accountManager->setNotificationKey($organization, 'notificationsLowCredit.percent_'.$percent);
            $accountManager->dispatch('billing.account.lowBalance', $event);

            $output->writeln(sprintf(
                '<info>Notify low by %s - %s</info> Current: %f € Last Positive Input: %f €',
                $percent,
                $organization->getContactName(),
                $organization->getAccount()->getAvailable(),
                $organization->getAccount()->getLastPositiveInput()
            ));
        }

    }
}

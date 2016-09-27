<?php

namespace WobbleCode\BillingBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Guzzle\Http\Client;

/**
 * Create a command line
 */
class ExchangeRateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('exchange-rate:update')
            ->setDescription('Update the exchange rate')
            ->addArgument(
                'from',
                InputArgument::OPTIONAL,
                'Currency code base',
                'USD'
            )
            ->addArgument(
                'to',
                InputArgument::OPTIONAL,
                'Currency codes by comma separated',
                'EUR'
            )
            ->addArgument(
                'date',
                InputArgument::OPTIONAL,
                'Date of currency exchange',
                'now'
            )
            ->addOption(
                'source',
                null,
                inputOption::VALUE_REQUIRED,
                'Select source for exchange rate, openexchangerates or yahoo',
                'openexchangerates'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getOption('source');

        if ($source === 'openexchangerates') {
            $results = $this->openExchangeRatesOption($input, $output);
        } else if ($source === 'yahoo') {
            $results = $this->yahooOption($input, $output);
        } else {
            throw new \RunTimeException(
                'The source values are openexchangerates or yahoo'
            );
        }

        $er = $this->getContainer()->get('wobblecode_billing.exchange_rate');
        $er->updateRates($input->getArgument('from'), $results, new \Datetime($input->getArgument('date')));
    }

    protected function openExchangeRatesOption(InputInterface $input, OutputInterface $output)
    {
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');
        $date = $input->getArgument('date');
        $client = new Client('http://openexchangerates.org/api');
        $apiKey = '1bba95ce633c4112be1808e1119c5c2b';

        $this->currencyCodeValidation($from, explode(',', $to));

        if ($date === 'now') {
            $request = $client
                ->get('/latest.json?app_id='.$apiKey);
        } else {
            $request = $client
                ->get('/historical/'.$date.'.json?app_id='.$apiKey);
        }

        $data = $request->send()->json();
        $results = array();

        foreach ($data['rates'] as $currency => $ratio) {
            $output->writeln($currency.' = <info>'.$ratio.'</info>');
            $results[$currency] = $ratio;
        }

        return $results;
    }

    protected function yahooOption(InputInterface $input, OutputInterface $output)
    {
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');
        $client = new Client('http://query.yahooapis.com/v1/public/yql');
        $get = '?q=select%20%2a%20from%20yahoo.finance.xchange%20where%20pair%20in%20%28';
        $finalCodes = explode(',', $to);

        $this->currencyCodeValidation($from, $finalCodes);

        foreach ($finalCodes as $key => $finalCode) {
            $get = ($key != 0)? $get.',%20' : $get;
            $get = $get.'"'.$from.$finalCode.'"';
        }

        $get = $get.'%29&env=store://datatables.org/alltableswithkeys';
        $request = $client->get($get);
        $xml = $request->send()->xml();
        $results = array();

        foreach ($xml as $key => $rate) {
            foreach ($rate as $key => $value) {
                $currency = explode(' ', $value->Name);
                $output->writeln(end($currency).' = <info>'.$value->Rate.'</info>');
                $results[end($currency)] = $value->Rate;
            }
        }

        return $results;
    }

    protected function currencyCodeValidation ($from, $finalCodes)
    {
        $client = new Client('http://openexchangerates.org/api');
        $currencies = $client
                ->get('/currencies.json')->send()->json();

        if (!array_key_exists($from, $currencies)) {
            throw new \RunTimeException(
                'The first argument must be a currency code.'
                .' Check codes: http://openexchangerates.org/api/currencies.json'
            );
        }

        foreach ($finalCodes as $key => $code) {
            if (!array_key_exists($code, $currencies)) {
                throw new \RunTimeException(
                    'The second argument must be currency codes by comma separated.'
                    .' Check codes: http://openexchangerates.org/api/currencies.json'
                );
            }
        }
    }
}

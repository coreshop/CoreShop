<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Console\Command;

use CoreShop\Exception;
use CoreShop\Model\Currency;
use CoreShop\Model\Currency\ExchangeRates;
use CoreShop\Tool;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateExchangeRatesCommand
 * @package CoreShop\Console\Command
 */
class UpdateExchangeRatesCommand extends AbstractCommand
{
    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:exchangerates')
            ->setDescription('Update Exchange Rates')
            ->addOption(
                'provider', 'p',
                InputOption::VALUE_OPTIONAL,
                'Update using Provider'
            )
            ->addOption(
                'currency', 'c',
                InputOption::VALUE_OPTIONAL,
                'Currency which should be fetched. '
            );
    }

    /**
     * execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Exception
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $provider = $input->getOption('provider');
        $currencyId = $input->getOption('currency');
        $currency = null;
        $baseCurrency = Tool::getBaseCurrency();

        if (!$provider) {
            $provider = ExchangeRates::getSystemProvider();
        }

        if (is_null($provider) || !ExchangeRates::providerExists($provider)) {
            throw new Exception("Provider $provider not found");
        }

        if ($currencyId) {
            $currency = Currency::getById($currencyId);

            if (!$currency instanceof Currency) {
                throw new Exception("Currency $currencyId not found");
            }
        }

        if ($currency instanceof Currency) {
            $currencies = array($currency);
        } else {
            //Do all activated currencies
            $currencies = Currency::getAvailable();
        }

        foreach ($currencies as $currency) {
            if ($currency->getId() === $baseCurrency->getId()) {
                continue;
            }

            try {
                $rate = ExchangeRates::updateExchangeRateForCurrency($provider, $currency);

                $output->writeLn('Update Exchange Rate for Currency '.$currency->getName().' ('.$currency->getIsoCode().') to: '.$rate);
            } catch (Exception $ex) {
                $output->writeln('<error>'.$ex->getMessage().'</error>');
            }
        }
    }
}

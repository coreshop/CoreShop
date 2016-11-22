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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Currency;

use CoreShop\Exception;
use CoreShop\Model\Configuration;
use CoreShop\Model\Currency;
use CoreShop\Tools;
use Ivory\HttpAdapter\FileGetContentsHttpAdapter;
use Pimcore\Logger;
use Swap\Model\CurrencyPair;
use Swap\Provider\AbstractProvider;
use Swap\Swap;

/**
 * Class ExchangeRates
 * @package CoreShop\Model\Currency
 */
class ExchangeRates
{
    /**
     * List of supported providers.
     *
     * @var array
     */
    public static $providerList = array(
        'CentralBankOfRepulicTurkey' => 'CentralBankOfRepublicTurkeyProvider',
        'EuropeanCentralBank' => 'EuropeanCentralBankProvider',
        'GoogleFinance' => 'GoogleFinanceProvider',
        'NationalBankOfRomania' => 'NationalBankOfRomaniaProvider',
        'YahooFinance' => 'YahooFinanceProvider',
        'WebserviceX' => 'WebserviceXProvider',
    );

    /**
     * @return ExchangeRates
     */
    public static function getInstance()
    {
        return Tools::createObject(static::class);
    }

    /**
     * get configured provider.
     *
     * @return mixed|null
     */
    public function getSystemProvider()
    {
        return Configuration::get('SYSTEM.CURRENCY.EXCHANGE_RATE_PROVIDER');
    }

    /**
     * maintenance job.
     */
    public function maintenance()
    {
        $lastUpdate = Configuration::get('SYSTEM.CURRENCY.LAST_EXCHANGE_UPDATE');

        if (!$lastUpdate) {
            $lastUpdate = 0;
        }

        $timeDiff = time() - $lastUpdate;

        //since maintenance runs every 5 minutes, we need to check if the last update was 24 hours ago
        if ($timeDiff > 24 * 60 * 60) {
            $provider = $this->getSystemProvider();
            $currencies = Currency::getAvailable();

            foreach ($currencies as $currency) {
                try {
                    $this->updateExchangeRateForCurrency($provider, $currency);
                } catch (Exception $ex) {
                    Logger::err($ex);
                }
            }
        }

        Configuration::set('SYSTEM.CURRENCY.LAST_EXCHANGE_UPDATE', time());
    }

    /**
     * update exchange rate for currency.
     *
     * @param $provider
     * @param Currency $toCurrency
     *
     * @throws Exception
     *
     * @return float
     */
    public function updateExchangeRateForCurrency($provider, Currency $toCurrency)
    {
        $baseCurrency = \CoreShop::getTools()->getBaseCurrency();

        $rate = $this->getExchangeRateForCurrencyPair($provider, $baseCurrency, $toCurrency);

        $toCurrency->setExchangeRate($rate);
        $toCurrency->save();

        return $rate;
    }

    /**
     * get exchange rate for currency pair from a specific provider
     *
     * @param $provider
     * @param Currency $fromCurrency
     * @param Currency $toCurrency
     *
     * @throws Exception
     *
     * @returns float
     */
    public function getExchangeRateForCurrencyPair($provider, Currency $fromCurrency, Currency $toCurrency)
    {
        $provider = $this->getProvider($provider);

        $swap = new Swap($provider);
        $currencyPair = new CurrencyPair($fromCurrency->getIsoCode(), $toCurrency->getIsoCode());

        try {
            $rate = $swap->quote($currencyPair);
            $rate = floatval($rate->getValue());

            if ($rate < 0) {
                throw new Exception('rate is smaller than 0');
            }

            return $rate;
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * get provider class object.
     *
     * @param $providerName string
     *
     * @return AbstractProvider
     *
     * @throws Exception
     */
    public function getProvider($providerName)
    {
        $providerClass = '\\Swap\\Provider\\'.self::$providerList[$providerName];

        if (!self::providerExists($providerName)) {
            throw new Exception('Provider with class '.$providerClass.' not found');
        }

        return new $providerClass(new FileGetContentsHttpAdapter());
    }

    /**
     * check if provider exists.
     *
     * @param $providerName
     *
     * @return bool
     */
    public static function providerExists($providerName)
    {
        $providerClass = '\\Swap\\Provider\\'.self::$providerList[$providerName];

        if (\Pimcore\Tool::classExists($providerClass)) {
            return true;
        }

        return false;
    }
}

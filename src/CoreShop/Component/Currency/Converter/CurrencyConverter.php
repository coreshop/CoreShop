<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Currency\Converter;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Model\ExchangeRateInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Repository\ExchangeRateRepositoryInterface;

final class CurrencyConverter implements CurrencyConverterInterface
{
    /**
     * @var ExchangeRateRepositoryInterface
     */
    private $exchangeRateRepository;

    /**
     * @var CurrencyRepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var array
     */
    private $cache;

    public function __construct(ExchangeRateRepositoryInterface $exchangeRateRepository, CurrencyRepositoryInterface $currencyRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(int $amount, string $fromCurrencyCode, string $toCurrencyCode): int
    {
        if ($fromCurrencyCode === $toCurrencyCode) {
            return $amount;
        }

        $exchangeRate = $this->getExchangeRate($fromCurrencyCode, $toCurrencyCode);

        if (null === $exchangeRate) {
            return $amount;
        }

        if ($exchangeRate->getFromCurrency()->getIsoCode() === $fromCurrencyCode) {
            return (int) round($amount * $exchangeRate->getExchangeRate());
        }

        return (int) round($amount / $exchangeRate->getExchangeRate());
    }

    /**
     * @param string $fromCode
     * @param string $toCode
     *
     * @return ExchangeRateInterface|null
     */
    private function getExchangeRate($fromCode, $toCode): ?ExchangeRateInterface
    {
        $fromToIndex = $this->createIndex($fromCode, $toCode);

        if (isset($this->cache[$fromToIndex])) {
            return $this->cache[$fromToIndex];
        }

        $toFromIndex = $this->createIndex($fromCode, $toCode);

        if (isset($this->cache[$toFromIndex])) {
            return $this->cache[$toFromIndex];
        }

        $fromCurrency = $this->currencyRepository->getByCode($fromCode);
        $toCurrency = $this->currencyRepository->getByCode($toCode);

        if (null !== $fromCurrency && null !== $toCurrency) {
            return $this->cache[$toFromIndex] = $this->exchangeRateRepository->findOneWithCurrencyPair($fromCurrency, $toCurrency);
        }

        return null;
    }

    /**
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    private function createIndex($prefix, $suffix): string
    {
        return sprintf('%s-%s', $prefix, $suffix);
    }
}

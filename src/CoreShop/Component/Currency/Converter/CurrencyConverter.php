<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Currency\Converter;

use CoreShop\Component\Currency\Model\ExchangeRateInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Repository\ExchangeRateRepositoryInterface;

final class CurrencyConverter implements CurrencyConverterInterface
{
    private array $cache = [];

    public function __construct(
        private ExchangeRateRepositoryInterface $exchangeRateRepository,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function convert(int $value, string $fromCurrencyCode, string $toCurrencyCode): int
    {
        if ($fromCurrencyCode === $toCurrencyCode) {
            return $value;
        }

        $exchangeRate = $this->getExchangeRate($fromCurrencyCode, $toCurrencyCode);

        if (null === $exchangeRate) {
            return $value;
        }

        if ($exchangeRate->getFromCurrency()->getIsoCode() === $fromCurrencyCode) {
            return (int) round($value * $exchangeRate->getExchangeRate());
        }

        return (int) round($value / $exchangeRate->getExchangeRate());
    }

    private function getExchangeRate(string $fromCode, string $toCode): ?ExchangeRateInterface
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

    private function createIndex(string $prefix, string $suffix): string
    {
        return sprintf('%s-%s', $prefix, $suffix);
    }
}

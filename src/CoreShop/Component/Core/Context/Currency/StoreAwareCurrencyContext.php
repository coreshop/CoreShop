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

namespace CoreShop\Component\Core\Context\Currency;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

final class StoreAwareCurrencyContext implements CurrencyContextInterface
{
    public function __construct(
        private CurrencyContextInterface $currencyContext,
        private StoreContextInterface $storeContext,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function getCurrency(): CurrencyInterface
    {
        /** @var StoreInterface $store */
        $store = $this->storeContext->getStore();

        try {
            $currency = $this->currencyContext->getCurrency();

            if (!$this->isAvailableCurrency($currency, $store)) {
                return $store->getCurrency();
            }

            return $currency;
        } catch (CurrencyNotFoundException) {
            return $store->getCurrency();
        }
    }

    private function isAvailableCurrency(CurrencyInterface $currency, StoreInterface $store): bool
    {
        return in_array($currency->getIsoCode(), array_map(function (CurrencyInterface $currency) {
            return $currency->getIsoCode();
        }, $this->getCurrenciesForStore($store)), true);
    }

    /**
     * @return CurrencyInterface[]
     */
    private function getCurrenciesForStore(StoreInterface $store): array
    {
        return $this->currencyRepository->findActiveForStore($store);
    }
}

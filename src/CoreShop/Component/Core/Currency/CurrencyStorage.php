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

namespace CoreShop\Component\Core\Currency;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Resource\Storage\StorageInterface;
use CoreShop\Component\Store\Model\StoreInterface;

final class CurrencyStorage implements CurrencyStorageInterface
{
    public function __construct(
        private StorageInterface $storage,
        private CurrencyRepositoryInterface $currencyRepository,
    ) {
    }

    public function set(StoreInterface $store, CurrencyInterface $currency): void
    {
        if ($this->isBaseCurrency($currency, $store) || !$this->isAvailableCurrency($currency, $store)) {
            $this->storage->remove($this->provideKey($store));

            return;
        }

        $this->storage->set($this->provideKey($store), $currency->getId());
    }

    public function get(StoreInterface $store): CurrencyInterface
    {
        if ($this->storage->get($this->provideKey($store))) {
            $currency = $this->currencyRepository->find($this->storage->get($this->provideKey($store)));

            if ($currency instanceof CurrencyInterface) {
                return $currency;
            }
        }

        throw new CurrencyNotFoundException();
    }

    private function provideKey(StoreInterface $store): string
    {
        return '_currency_' . $store->getId();
    }

    private function isBaseCurrency(CurrencyInterface $currency, StoreInterface $store): bool
    {
        if ($store instanceof \CoreShop\Component\Core\Model\StoreInterface) {
            return $store->getCurrency()->getId() === $currency->getId();
        }

        return false;
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

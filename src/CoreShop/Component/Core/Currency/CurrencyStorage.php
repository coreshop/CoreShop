<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Currency;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Resource\Storage\StorageInterface;
use CoreShop\Component\Store\Model\StoreInterface;

final class CurrencyStorage implements CurrencyStorageInterface
{
    private StorageInterface $storage;
    private CurrencyRepositoryInterface $currencyRepository;

    public function __construct(StorageInterface $storage, CurrencyRepositoryInterface $currencyRepository)
    {
        $this->storage = $storage;
        $this->currencyRepository = $currencyRepository;
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
     * @param StoreInterface $store
     *
     * @return CurrencyInterface[]
     */
    private function getCurrenciesForStore(StoreInterface $store): array
    {
        return $this->currencyRepository->findActiveForStore($store);
    }
}

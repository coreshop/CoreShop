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

namespace CoreShop\Component\Core\Currency;

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Resource\Storage\StorageInterface;
use CoreShop\Component\Store\Model\StoreInterface;

final class CurrencyStorage implements CurrencyStorageInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var CurrencyRepositoryInterface
     */
    private $currencyRepository;

    /**
     * @param StorageInterface            $storage
     * @param CurrencyRepositoryInterface $currencyRepository
     */
    public function __construct(StorageInterface $storage, CurrencyRepositoryInterface $currencyRepository)
    {
        $this->storage = $storage;
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function set(StoreInterface $store, CurrencyInterface $currency)
    {
        if ($this->isBaseCurrency($currency, $store) || !$this->isAvailableCurrency($currency, $store)) {
            $this->storage->remove($this->provideKey($store));

            return;
        }

        $this->storage->set($this->provideKey($store), $currency->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function get(StoreInterface $store)
    {
        if ($this->storage->get($this->provideKey($store))) {
            $currency = $this->currencyRepository->find($this->storage->get($this->provideKey($store)));

            if ($currency instanceof CurrencyInterface) {
                return $currency;
            }
        }

        throw new CurrencyNotFoundException();
    }

    /**
     * {@inheritdoc}
     */
    private function provideKey(StoreInterface $store)
    {
        return '_currency_' . $store->getId();
    }

    /**
     * @param CurrencyInterface $currency
     * @param StoreInterface    $store
     *
     * @return bool
     */
    private function isBaseCurrency(CurrencyInterface $currency, StoreInterface $store)
    {
        if ($store instanceof \CoreShop\Component\Core\Model\StoreInterface) {
            return $store->getCurrency()->getId() === $currency->getId();
        }

        return false;
    }

    /**
     * @param CurrencyInterface $currency
     * @param StoreInterface    $store
     *
     * @return bool
     */
    private function isAvailableCurrency(CurrencyInterface $currency, StoreInterface $store)
    {
        return in_array($currency->getIsoCode(), array_map(function (CurrencyInterface $currency) {
            return $currency->getIsoCode();
        }, $this->getCurrenciesForStore($store)));
    }

    /**
     * @param StoreInterface $store
     *
     * @return CurrencyInterface[]
     */
    private function getCurrenciesForStore(StoreInterface $store)
    {
        return $this->currencyRepository->findActiveForStore($store);
    }
}

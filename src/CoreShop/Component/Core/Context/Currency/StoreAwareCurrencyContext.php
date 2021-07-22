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

namespace CoreShop\Component\Core\Context\Currency;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

final class StoreAwareCurrencyContext implements CurrencyContextInterface
{
    private CurrencyContextInterface $currencyContext;
    private StoreContextInterface $storeContext;
    private CurrencyRepositoryInterface $currencyRepository;

    public function __construct(
        CurrencyContextInterface $currencyContext,
        StoreContextInterface $storeContext,
        CurrencyRepositoryInterface $currencyRepository
    ) {
        $this->currencyContext = $currencyContext;
        $this->storeContext = $storeContext;
        $this->currencyRepository = $currencyRepository;
    }

    public function getCurrency(): CurrencyInterface
    {
        /** @var StoreInterface $store */
        $store = $this->storeContext->getStore();

        try {
            $currency = $this->currencyContext->getCurrency();

            if (!$currency instanceof CurrencyInterface || !$this->isAvailableCurrency($currency, $store)) {
                return $store->getCurrency();
            }

            return $currency;
        } catch (CurrencyNotFoundException $exception) {
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
     * @param StoreInterface $store
     *
     * @return CurrencyInterface[]
     */
    private function getCurrenciesForStore(StoreInterface $store): array
    {
        return $this->currencyRepository->findActiveForStore($store);
    }
}

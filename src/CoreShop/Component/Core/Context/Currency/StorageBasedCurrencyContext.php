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

namespace CoreShop\Component\Core\Context\Currency;

use CoreShop\Component\Core\Currency\CurrencyStorageInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Model\StoreInterface;

final class StorageBasedCurrencyContext implements CurrencyContextInterface
{
    private $storeContext;
    private $currencyStorage;

    public function __construct(StoreContextInterface $storeContext, CurrencyStorageInterface $currencyStorage)
    {
        $this->storeContext = $storeContext;
        $this->currencyStorage = $currencyStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency(): CurrencyInterface
    {
        /** @var StoreInterface $store */
        $store = $this->storeContext->getStore();

        if (null === $store) {
            throw new CurrencyNotFoundException();
        }

        $currency = $this->currencyStorage->get($store);

        if (null === $currency) {
            throw CurrencyNotFoundException::notFound($currency);
        }

        return $currency;
    }
}

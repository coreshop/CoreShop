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

use CoreShop\Component\Core\Currency\CurrencyStorageInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;

final class StorageBasedCurrencyContext implements CurrencyContextInterface
{
    public function __construct(
        private StoreContextInterface $storeContext,
        private CurrencyStorageInterface $currencyStorage,
    ) {
    }

    public function getCurrency(): CurrencyInterface
    {
        try {
            $store = $this->storeContext->getStore();
        } catch (StoreNotFoundException $ex) {
            throw new CurrencyNotFoundException(null, $ex);
        }

        try {
            $currency = $this->currencyStorage->get($store);
        } catch (CurrencyNotFoundException $ex) {
            throw new CurrencyNotFoundException(null, $ex);
        }

        return $currency;
    }
}

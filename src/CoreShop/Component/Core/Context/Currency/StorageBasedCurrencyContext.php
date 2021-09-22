<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Context\Currency;

use CoreShop\Component\Core\Currency\CurrencyStorageInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;

final class StorageBasedCurrencyContext implements CurrencyContextInterface
{
    private StoreContextInterface $storeContext;
    private CurrencyStorageInterface $currencyStorage;

    public function __construct(StoreContextInterface $storeContext, CurrencyStorageInterface $currencyStorage)
    {
        $this->storeContext = $storeContext;
        $this->currencyStorage = $currencyStorage;
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
        }
        catch (CurrencyNotFoundException $ex) {
            throw new CurrencyNotFoundException(null, $ex);
        }

        return $currency;
    }
}

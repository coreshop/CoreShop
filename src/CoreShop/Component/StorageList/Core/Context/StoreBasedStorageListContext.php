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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\StorageList\Core\Context;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Currency\Context\CurrencyNotFoundException;
use CoreShop\Component\Customer\Model\CustomerAwareInterface;
use CoreShop\Component\Locale\Context\LocaleNotFoundException;
use CoreShop\Component\StorageList\Context\StorageListContextInterface;
use CoreShop\Component\StorageList\Context\StorageListNotFoundException;
use CoreShop\Component\StorageList\Model\StorageListInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreAwareInterface;

final class StoreBasedStorageListContext implements StorageListContextInterface
{
    private ?StorageListInterface $storageList = null;

    public function __construct(
        private StorageListContextInterface $context,
        private ShopperContextInterface $shopperContext,
    ) {
    }

    public function getStorageList(): StorageListInterface
    {
        if (null !== $this->storageList) {
            return $this->storageList;
        }

        /**
         * @var StorageListInterface $storageList
         */
        $storageList = $this->context->getStorageList();

        if (!$storageList instanceof StoreAwareInterface) {
            throw new StorageListNotFoundException();
        }

        if (!$storageList instanceof CustomerAwareInterface) {
            throw new StorageListNotFoundException();
        }

        try {
            /** @var StoreInterface $store */
            $store = $this->shopperContext->getStore();
            $storageList->setStore($store);
        } catch (StoreNotFoundException|CurrencyNotFoundException|LocaleNotFoundException $exception) {
            throw new StorageListNotFoundException('CoreShop was not able to prepare the wishlist.', $exception);
        }

        if ($this->shopperContext->hasCustomer()) {
            /**
             * @var CustomerInterface $customer
             */
            $customer = $this->shopperContext->getCustomer();
            $storageList->setCustomer($customer);
        }

        $this->storageList = $storageList;

        return $storageList;
    }
}

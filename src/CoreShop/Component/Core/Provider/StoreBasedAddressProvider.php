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

namespace CoreShop\Component\Core\Provider;

use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;

class StoreBasedAddressProvider implements AddressProviderInterface
{
    public function __construct(
        private FactoryInterface $addressFactory,
        private ShopperContextInterface $shopperContext,
    ) {
    }

    public function getAddress(OrderInterface $cart): ?AddressInterface
    {
        $store = $cart->getStore();
        if ($store instanceof StoreInterface) {
            $address = $this->addressFactory->createNew();

            try {
                $address->setCountry($this->shopperContext->getCountry());
            } catch (StoreNotFoundException) {
                $address->setCountry($store->getBaseCountry());
            } catch (CountryNotFoundException) {
                $address->setCountry($store->getBaseCountry());
            }

            return $address;
        }

        return null;
    }
}

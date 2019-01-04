<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Provider;

use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;

class StoreBasedAddressProvider implements AddressProviderInterface
{
    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @param FactoryInterface        $addressFactory
     * @param ShopperContextInterface $shopperContext
     */
    public function __construct(FactoryInterface $addressFactory, ShopperContextInterface $shopperContext)
    {
        $this->addressFactory = $addressFactory;
        $this->shopperContext = $shopperContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress(CartInterface $cart)
    {
        if ($cart->getStore() instanceof StoreInterface) {
            $address = $this->addressFactory->createNew();

            try {
                $address->setCountry($this->shopperContext->getCountry());
            } catch (StoreNotFoundException $ex) {
                $address->setCountry($cart->getStore()->getBaseCountry());
            } catch (CountryNotFoundException $ex) {
                $address->setCountry($cart->getStore()->getBaseCountry());
            }

            return $address;
        }

        return null;
    }
}

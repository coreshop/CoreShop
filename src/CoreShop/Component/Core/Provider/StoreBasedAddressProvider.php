<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Provider;

use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

class StoreBasedAddressProvider implements AddressProviderInterface
{
    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @param FactoryInterface $addressFactory
     */
    public function __construct(FactoryInterface $addressFactory)
    {
        $this->addressFactory = $addressFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress(CartInterface $cart)
    {
        if ($cart->getStore() instanceof StoreInterface) {
            $address = $this->addressFactory->createNew();
            $address->setCountry($cart->getStore()->getBaseCountry());

            return $address;
        }

        return null;
    }
}

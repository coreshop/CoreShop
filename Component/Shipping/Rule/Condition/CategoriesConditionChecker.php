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

namespace CoreShop\Component\Shipping\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

class CategoriesConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, array $configuration)
    {
        $cartItems = $cart->getItems();

        foreach ($cartItems as $item) {
            if ($item->getProduct() instanceof ProductInterface) {
                foreach ($item->getProduct()->getCategories() as $category) {
                    if ($category instanceof ResourceInterface) {
                        if (in_array($category->getId(), $configuration['categories'])) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}

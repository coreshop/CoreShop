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

class DimensionConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isShippingRuleValid(CarrierInterface $carrier, CartInterface $cart, AddressInterface $address, array $configuration)
    {
        $height = $configuration['height'];
        $width = $configuration['width'];
        $depth = $configuration['depth'];

        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            if ($product instanceof ProductInterface) {
                if ($height > 0) {
                    if ($product->getHeight() > $height) {
                        return false;
                    }
                }

                if ($depth > 0) {
                    if ($product->getDepth() > $depth) {
                        return false;
                    }
                }

                if ($width > 0) {
                    if ($product->getWidth() > $width) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}

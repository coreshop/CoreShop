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

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Exception\NoDiscountPriceFoundException;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositeDiscountPriceCalculator implements ProductDiscountPriceCalculatorInterface
{
    public function __construct(
        protected PrioritizedServiceRegistryInterface $discountPriceCalculator,
    ) {
    }

    public function getDiscountPrice(ProductInterface $product, array $context): int
    {
        $price = null;

        /**
         * @var ProductDiscountPriceCalculatorInterface $calculator
         */
        foreach ($this->discountPriceCalculator->all() as $calculator) {
            try {
                $actionPrice = $calculator->getDiscountPrice($product, $context);
                $price = $actionPrice;
            } catch (NoDiscountPriceFoundException) {
            }
        }

        if (null === $price) {
            throw new NoDiscountPriceFoundException(__CLASS__);
        }

        return $price;
    }
}

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

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Exception\NoDiscountPriceFoundException;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;

class CompositeDiscountPriceCalculator implements ProductDiscountPriceCalculatorInterface
{
    public function __construct(protected PrioritizedServiceRegistryInterface $discountPriceCalculator)
    {
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

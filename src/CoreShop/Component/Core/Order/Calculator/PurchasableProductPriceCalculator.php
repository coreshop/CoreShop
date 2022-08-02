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

namespace CoreShop\Component\Core\Order\Calculator;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Calculator\PurchasablePriceCalculatorInterface;
use CoreShop\Component\Order\Exception\NoPurchasablePriceFoundException;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Product\Exception\NoPriceFoundException;

final class PurchasableProductPriceCalculator implements PurchasablePriceCalculatorInterface
{
    public function __construct(private ProductPriceCalculatorInterface $productPriceCalculator)
    {
    }

    public function getPrice(PurchasableInterface $purchasable, array $context, bool $includingDiscounts = false): int
    {
        if ($purchasable instanceof ProductInterface) {
            try {
                return $this->productPriceCalculator->getPrice($purchasable, $context, $includingDiscounts);
            } catch (NoPriceFoundException) {
            }
        }

        throw new NoPurchasablePriceFoundException(__CLASS__);
    }
}

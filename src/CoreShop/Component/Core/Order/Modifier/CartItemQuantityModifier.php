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

namespace CoreShop\Component\Core\Order\Modifier;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\StorageListItemQuantityModifierInterface;
use Webmozart\Assert\Assert;

class CartItemQuantityModifier implements StorageListItemQuantityModifierInterface
{
    /**
     * @param StorageListItemInterface $item
     * @param float                    $targetQuantity
     */
    public function modify(StorageListItemInterface $item, float $targetQuantity)
    {
        /**
         * @var CartItemInterface $item
         */
        Assert::isInstanceOf($item, CartItemInterface::class);

        $cleanTargetQuantity = $this->roundQuantity($item, $targetQuantity);

        $item->setQuantity($cleanTargetQuantity);

        if ($item->hasUnitDefinition()) {
            $item->setDefaultUnitQuantity($item->getUnitDefinition()->getConversionRate() * $item->getQuantity());
        } else {
            $item->setDefaultUnitQuantity($item->getQuantity());
        }
    }

    /**
     * @param StorageListItemInterface $item
     * @param float $targetQuantity
     *
     * @return float
     */
    public function roundQuantity(StorageListItemInterface $item, float $targetQuantity)
    {
        if (!$item instanceof CartItemInterface) {
            return $targetQuantity;
        }

        if (!$item->hasUnitDefinition()) {
            return $targetQuantity;
        }

        $product = $item->getProduct();
        if (!$product instanceof ProductInterface) {
            return $targetQuantity;
        }

        $scale = $this->getScale($item);
        if ($scale === null) {
            return $targetQuantity;
        }

        $quantity = (float) str_replace(',', '.', $targetQuantity);
        $formattedQuantity = round($quantity, $scale, PHP_ROUND_HALF_UP);

        if ($quantity !== $formattedQuantity) {
            return $formattedQuantity;
        }

        return $targetQuantity;
    }

    /**
     * @param CartItemInterface $cartItem
     *
     * @return int|null
     */
    protected function getScale(CartItemInterface $cartItem)
    {
        $productUnitDefinition = $cartItem->getUnitDefinition();
        if (!$productUnitDefinition instanceof ProductUnitDefinitionInterface) {
            return null;
        }

        $precision = $productUnitDefinition->getPrecision();

        if (is_int($precision)) {
            return $precision;
        }

        return null;
    }
}

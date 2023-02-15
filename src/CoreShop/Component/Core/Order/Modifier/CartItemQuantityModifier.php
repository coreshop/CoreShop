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

namespace CoreShop\Component\Core\Order\Modifier;

use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\StorageList\Model\StorageListItemInterface;
use CoreShop\Component\StorageList\StorageListItemQuantityModifierInterface;
use Webmozart\Assert\Assert;

class CartItemQuantityModifier implements StorageListItemQuantityModifierInterface
{
    public function modify(StorageListItemInterface $item, float $targetQuantity): void
    {
        /**
         * @var OrderItemInterface $item
         */
        Assert::isInstanceOf($item, OrderItemInterface::class);

        $currentQuantity = $item->getQuantity();
        if (0 >= $targetQuantity || $currentQuantity === $targetQuantity) {
            return;
        }

        $cleanTargetQuantity = $this->roundQuantity($item, $targetQuantity);

        $item->setQuantity($cleanTargetQuantity);

        if ($item->hasUnitDefinition()) {
            $item->setDefaultUnitQuantity($item->getUnitDefinition()->getConversionRate() * $item->getQuantity());
        } else {
            $item->setDefaultUnitQuantity($item->getQuantity());
        }
    }

    public function roundQuantity(StorageListItemInterface $item, float $targetQuantity): float
    {
        if (!$item instanceof OrderItemInterface) {
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

        $quantity = (float) str_replace(',', '.', (string) $targetQuantity);
        $formattedQuantity = round($quantity, $scale, \PHP_ROUND_HALF_UP);

        if ($quantity !== $formattedQuantity) {
            return $formattedQuantity;
        }

        return $targetQuantity;
    }

    protected function getScale(OrderItemInterface $cartItem): ?int
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

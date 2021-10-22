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

        $quantity = (float)str_replace(',', '.', (string)$targetQuantity);
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

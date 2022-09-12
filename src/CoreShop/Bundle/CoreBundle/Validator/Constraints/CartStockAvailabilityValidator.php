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

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Inventory\Checker\AvailabilityCheckerInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartStockAvailabilityValidator extends ConstraintValidator
{
    public function __construct(private AvailabilityCheckerInterface $availabilityChecker)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        /**
         * @var OrderInterface        $value
         * @var CartStockAvailability $constraint
         */
        Assert::isInstanceOf($value, OrderInterface::class);
        Assert::isInstanceOf($constraint, CartStockAvailability::class);

        $isStockSufficient = true;
        $productsChecked = [];
        $insufficientProduct = null;

        /**
         * @var OrderItemInterface $cartItem
         */
        foreach ($value->getItems() as $cartItem) {
            $product = $cartItem->getProduct();

            if (!$product instanceof StockableInterface) {
                continue;
            }

            if (in_array($product->getId(), $productsChecked, true)) {
                continue;
            }

            $isStockSufficient = $this->availabilityChecker->isStockSufficient(
                $product,
                $this->getExistingCartItemQuantityFromCart($value, $cartItem),
            );

            $productsChecked[] = $product->getId();

            if (!$isStockSufficient) {
                $insufficientProduct = $product;

                break;
            }
        }

        if (!$isStockSufficient && $insufficientProduct instanceof StockableInterface) {
            $this->context->addViolation(
                $constraint->message,
                ['%stockable%' => $insufficientProduct->getInventoryName()],
            );
        }
    }

    private function getExistingCartItemQuantityFromCart(OrderInterface $cart, OrderItemInterface $cartItem): float
    {
        $product = $cartItem->getProduct();
        $quantity = $cartItem->getDefaultUnitQuantity() ?? 0;

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            if ($item->getId() === $cartItem->getId()) {
                continue;
            }

            if ($item->getProduct() instanceof $product && $item->getProduct()->getId() === $product->getId()) {
                $quantity += $item->getDefaultUnitQuantity();
            }
        }

        return $quantity;
    }
}

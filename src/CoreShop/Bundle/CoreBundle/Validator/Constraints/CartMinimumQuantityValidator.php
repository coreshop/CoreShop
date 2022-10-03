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

use CoreShop\Bundle\CoreBundle\Validator\QuantityValidatorService;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class CartMinimumQuantityValidator extends ConstraintValidator
{
    public function __construct(
        private QuantityValidatorService $quantityValidatorService,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        /**
         * @var OrderInterface      $value
         * @var CartMinimumQuantity $constraint
         */
        Assert::isInstanceOf($value, OrderInterface::class);
        Assert::isInstanceOf($constraint, CartMinimumQuantity::class);

        $lowerThenMinimum = false;
        $productsChecked = [];
        $invalidProduct = null;
        $minLimit = 0;

        /**
         * @var OrderItemInterface $cartItem
         */
        foreach ($value->getItems() as $cartItem) {
            $product = $cartItem->getProduct();

            if (!$product instanceof StockableInterface) {
                continue;
            }

            if (!$product instanceof ProductInterface) {
                continue;
            }

            if (in_array($product->getId(), $productsChecked, true)) {
                continue;
            }

            if (!is_numeric($product->getMinimumQuantityToOrder())) {
                continue;
            }

            if ($product->getMinimumQuantityToOrder() <= 0) {
                continue;
            }

            $minLimit = $product->getMinimumQuantityToOrder();
            $lowerThenMinimum = $this->quantityValidatorService->isLowerThenMinLimit(
                $minLimit,
                $this->getExistingCartItemQuantityFromCart($value, $cartItem),
            );

            if (!in_array($product->getId(), $productsChecked, true)) {
                $productsChecked[] = $product->getId();
            }

            if ($lowerThenMinimum === true) {
                $invalidProduct = $product;

                break;
            }
        }

        if ($invalidProduct instanceof StockableInterface) {
            $this->context->addViolation(
                $constraint->messageBelowMinimum,
                [
                    '%stockable%' => $invalidProduct->getInventoryName(),
                    '%limit%' => $minLimit,
                ],
            );
        }
    }

    private function getExistingCartItemQuantityFromCart(OrderInterface $cart, OrderItemInterface $cartItem): float
    {
        $product = $cartItem->getProduct();
        $quantity = $cartItem->getDefaultUnitQuantity();

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

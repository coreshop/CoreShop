<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
    private $availabilityChecker;

    public function __construct(AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->availabilityChecker = $availabilityChecker;
    }

    public function validate($cart, Constraint $constraint): void
    {
        /**
         * @var OrderInterface        $cart
         * @var CartStockAvailability $constraint
         */
        Assert::isInstanceOf($cart, OrderInterface::class);
        Assert::isInstanceOf($constraint, CartStockAvailability::class);

        $isStockSufficient = true;
        $productsChecked = [];
        $insufficientProduct = null;

        /**
         * @var OrderItemInterface $cartItem
         */
        foreach ($cart->getItems() as $cartItem) {
            $product = $cartItem->getProduct();

            if (!$product instanceof StockableInterface) {
                continue;
            }

            if (in_array($product->getId(), $productsChecked, true)) {
                continue;
            }

            $isStockSufficient = $this->availabilityChecker->isStockSufficient(
                $product,
                $this->getExistingCartItemQuantityFromCart($cart, $cartItem)
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
                ['%stockable%' => $insufficientProduct->getInventoryName()]
            );
        }
    }

    private function getExistingCartItemQuantityFromCart(OrderInterface $cart, OrderItemInterface $cartItem): int
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

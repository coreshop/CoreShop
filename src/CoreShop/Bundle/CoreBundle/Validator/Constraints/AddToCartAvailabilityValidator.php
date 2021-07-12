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

use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Inventory\Checker\AvailabilityCheckerInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\StorageList\StorageListItemResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class AddToCartAvailabilityValidator extends ConstraintValidator
{
    private AvailabilityCheckerInterface $availabilityChecker;
    private StorageListItemResolverInterface $cartItemResolver;

    public function __construct(
        AvailabilityCheckerInterface $availabilityChecker,
        StorageListItemResolverInterface $cartItemResolver
    )
    {
        $this->availabilityChecker = $availabilityChecker;
        $this->cartItemResolver = $cartItemResolver;
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, AddToCartInterface::class);
        Assert::isInstanceOf($constraint, AddToCartAvailability::class);

        /**
         * @var PurchasableInterface $purchasable
         */
        $purchasable = $value->getCartItem()->getProduct();

        if (!$purchasable instanceof StockableInterface) {
            return;
        }

        /**
         * @var OrderItemInterface $cartItem
         */
        $cartItem = $value->getCartItem();

        /**
         * @var OrderInterface $cart
         */
        $cart = $value->getCart();

        $isStockSufficient = $this->availabilityChecker->isStockSufficient(
            $purchasable,
            $cartItem->getDefaultUnitQuantity() + $this->getExistingCartItemQuantityFromCart($cart, $cartItem)
        );

        if (!$isStockSufficient) {
            $this->context->addViolation(
                $constraint->message,
                ['%stockable%' => $purchasable->getInventoryName()]
            );
        }
    }

    private function getExistingCartItemQuantityFromCart(OrderInterface $cart, OrderItemInterface $cartItem): float
    {
        $product = $cartItem->getProduct();
        $quantity = 0;

        /**
         * @var OrderItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            if (!$product && $this->cartItemResolver->equals($item, $cartItem)) {
                return $item->getDefaultUnitQuantity() ?? 0.0;
            }

            if ($item->getProduct() instanceof $product && $item->getProduct()->getId() === $product->getId()) {
                $quantity += $item->getDefaultUnitQuantity() ?? 0.0;
            }
        }

        return $quantity;
    }
}

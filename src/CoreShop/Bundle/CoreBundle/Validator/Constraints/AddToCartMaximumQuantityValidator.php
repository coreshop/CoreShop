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
use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\StorageList\StorageListItemResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class AddToCartMaximumQuantityValidator extends ConstraintValidator
{
    public function __construct(
        private QuantityValidatorService $quantityValidatorService,
        private StorageListItemResolverInterface $cartItemResolver,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, AddToCartInterface::class);
        Assert::isInstanceOf($constraint, AddToCartMaximumQuantity::class);

        /**
         * @var PurchasableInterface $purchasable
         */
        $purchasable = $value->getCartItem()->getProduct();

        if (!$purchasable instanceof StockableInterface) {
            return;
        }

        if (!$purchasable instanceof ProductInterface) {
            return;
        }

        /**
         * @var OrderInterface $cart
         */
        $cart = $value->getCart();

        /**
         * @var OrderItemInterface $cartItem
         */
        $cartItem = $value->getCartItem();

        $quantity = $cartItem->getDefaultUnitQuantity() + $this->getExistingCartItemQuantityFromCart($cart, $cartItem);
        $maxLimit = $purchasable->getMaximumQuantityToOrder();

        if (null === $maxLimit) {
            return;
        }

        if ($maxLimit <= 0) {
            return;
        }

        if ($this->quantityValidatorService->isHigherThenMaxLimit($maxLimit, $quantity)) {
            $this->context->addViolation(
                $constraint->messageAboveMaximum,
                [
                    '%stockable%' => $purchasable->getInventoryName(),
                    '%limit%' => $maxLimit,
                ],
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

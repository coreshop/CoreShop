<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

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

final class AddToCartMinimumQuantityValidator extends ConstraintValidator
{
    public function __construct(private QuantityValidatorService $quantityValidatorService, private StorageListItemResolverInterface $cartItemResolver)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, AddToCartInterface::class);
        Assert::isInstanceOf($constraint, AddToCartMinimumQuantity::class);

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
        $minLimit = $purchasable->getMinimumQuantityToOrder();

        if (null === $minLimit) {
            return;
        }

        if ($minLimit <= 0) {
            return;
        }

        if ($this->quantityValidatorService->isLowerThenMinLimit($minLimit, $quantity)) {
            $this->context->addViolation(
                $constraint->messageBelowMinimum,
                [
                    '%stockable%' => $purchasable->getInventoryName(),
                    '%limit%' => $minLimit,
                ]
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
                return $item->getDefaultUnitQuantity();
            }

            if ($item->getProduct() instanceof $product && $item->getProduct()->getId() === $product->getId()) {
                $quantity += $item->getDefaultUnitQuantity();
            }
        }

        return $quantity;
    }
}

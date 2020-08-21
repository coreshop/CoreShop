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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use CoreShop\Bundle\CoreBundle\Validator\QuantityValidatorService;
use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Cart\CartItemResolver;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\StorageList\StorageListItemResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class AddToCartMaximumQuantityValidator extends ConstraintValidator
{
    private $quantityValidatorService;
    protected $cartItemResolver;

    public function __construct(
        QuantityValidatorService $quantityValidatorService,
        StorageListItemResolverInterface $cartItemResolver
    )
    {
        $this->quantityValidatorService = $quantityValidatorService;
        $this->cartItemResolver = $cartItemResolver;
    }

    public function validate($addToCartDto, Constraint $constraint): void
    {
        Assert::isInstanceOf($addToCartDto, AddToCartInterface::class);
        Assert::isInstanceOf($constraint, AddToCartMaximumQuantity::class);

        /**
         * @var PurchasableInterface $purchasable
         */
        $purchasable = $addToCartDto->getCartItem()->getProduct();

        if (!$purchasable instanceof StockableInterface) {
            return;
        }

        if (!$purchasable instanceof ProductInterface) {
            return;
        }

        /**
         * @var OrderInterface      $cart
         */
        $cart = $addToCartDto->getCart();

        /**
         * @var OrderItemInterface $cartItem
         */
        $cartItem = $addToCartDto->getCartItem();

        $quantity = $cartItem->getDefaultUnitQuantity() + $this->getExistingCartItemQuantityFromCart($cart, $cartItem);
        $maxLimit = $purchasable->getMaximumQuantityToOrder();

        if (null === $maxLimit) {
            return;
        }

        if ($maxLimit <= 0) {
            return;
        }

        if($this->quantityValidatorService->isHigherThenMaxLimit($maxLimit, $quantity)) {
            $this->context->addViolation(
                $constraint->messageAboveMaximum,
                [
                    '%stockable%' => $purchasable->getInventoryName(),
                    '%limit%' => $maxLimit,
                ]
            );
        }
    }

    private function getExistingCartItemQuantityFromCart(OrderInterface $cart, OrderItemInterface $cartItem): int
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

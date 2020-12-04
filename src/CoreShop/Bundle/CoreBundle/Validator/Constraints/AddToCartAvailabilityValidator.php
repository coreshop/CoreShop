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

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Inventory\Checker\AvailabilityCheckerInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Cart\CartItemResolver;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\StorageList\StorageListItemResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class AddToCartAvailabilityValidator extends ConstraintValidator
{
    /**
     * @var AvailabilityCheckerInterface
     */
    private $availabilityChecker;

    /**
     * @var StorageListItemResolverInterface
     */
    protected $cartItemResolver;

    /**
     * @param AvailabilityCheckerInterface     $availabilityChecker
     * @param StorageListItemResolverInterface $cartItemResolver
     */
    public function __construct(
        AvailabilityCheckerInterface $availabilityChecker,
        StorageListItemResolverInterface $cartItemResolver = null
    ) {
        $this->availabilityChecker = $availabilityChecker;

        if (null === $cartItemResolver) {
            @trigger_error(
                'Not passing a StorageListItemResolverInterface as second argument is deprecated since 2.1.1 and will be removed with 3.0.0',
                E_USER_DEPRECATED
            );

            $this->cartItemResolver = new CartItemResolver();
        } else {
            $this->cartItemResolver = $cartItemResolver;
        }
    }

    /**
     * @param mixed      $addToCartDto
     * @param Constraint $constraint
     */
    public function validate($addToCartDto, Constraint $constraint): void
    {
        Assert::isInstanceOf($addToCartDto, AddToCartInterface::class);
        Assert::isInstanceOf($constraint, AddToCartAvailability::class);

        /**
         * @var PurchasableInterface $purchasable
         */
        $purchasable = $addToCartDto->getCartItem()->getProduct();

        if (!$purchasable instanceof StockableInterface) {
            return;
        }

        /**
         * @var CartItemInterface $cartItem
         */
        $cartItem = $addToCartDto->getCartItem();

        /**
         * @var CartInterface $cart
         */
        $cart = $addToCartDto->getCart();

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

    /**
     * @param CartInterface     $cart
     * @param CartItemInterface $cartItem
     *
     * @return int
     */
    private function getExistingCartItemQuantityFromCart(CartInterface $cart, CartItemInterface $cartItem)
    {
        $product = $cartItem->getProduct();
        $quantity = 0;

        /**
         * @var CartItemInterface $item
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

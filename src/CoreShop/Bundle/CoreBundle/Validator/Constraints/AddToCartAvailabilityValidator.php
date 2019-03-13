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

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use CoreShop\Bundle\OrderBundle\DTO\AddToCartInterface;
use CoreShop\Component\Inventory\Checker\AvailabilityCheckerInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
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
     * @param AvailabilityCheckerInterface $availabilityChecker
     */
    public function __construct(AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * @param AddToCartInterface $addCartItemCommand
     *
     * {@inheritdoc}
     */
    public function validate($addCartItemCommand, Constraint $constraint): void
    {
        Assert::isInstanceOf($addCartItemCommand, AddToCartInterface::class);
        Assert::isInstanceOf($constraint, AddToCartAvailability::class);

        /**
         * @var PurchasableInterface $purchasable
         */
        $purchasable = $addCartItemCommand->getCartItem()->getProduct();

        if (!$purchasable instanceof StockableInterface) {
            return;
        }

        $isStockSufficient = $this->availabilityChecker->isStockSufficient(
            $purchasable,
            $addCartItemCommand->getCartItem()->getQuantity() + $this->getExistingCartItemQuantityFromCart($addCartItemCommand->getCart(),
                $addCartItemCommand->getCartItem())
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
     * @return int
     */
    private function getExistingCartItemQuantityFromCart(CartInterface $cart, CartItemInterface $cartItem)
    {
        /**
         * @var CartItemInterface $item
         */
        foreach ($cart->getItems() as $item) {
            if ($item->equals($cartItem)) {
                return $item->getQuantity();
            }
        }

        return 0;
    }
}

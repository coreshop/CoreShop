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
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Inventory\Checker\AvailabilityCheckerInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartStockAvailabilityValidator extends ConstraintValidator
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
     * {@inheritdoc}
     */
    public function validate($cart, Constraint $constraint): void
    {
        /**
         * @var CartInterface $cart
         * @var CartStockAvailability $constraint
         */
        Assert::isInstanceOf($cart, CartInterface::class);
        Assert::isInstanceOf($constraint, CartStockAvailability::class);

        $isStockSufficient = true;
        $productsChecked = [];
        $insufficientProduct = null;

        /**
         * @var CartItemInterface $cartItem
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

    private function getExistingCartItemQuantityFromCart(CartInterface $cart, CartItemInterface $cartItem)
    {
        $product = $cartItem->getProduct();
        $quantity = $cartItem->getDefaultUnitQuantity();

        /**
         * @var CartItemInterface $item
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

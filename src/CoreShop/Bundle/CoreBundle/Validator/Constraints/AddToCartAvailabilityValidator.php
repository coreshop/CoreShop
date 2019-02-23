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
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Inventory\Checker\AvailabilityCheckerInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use CoreShop\Component\Order\Model\CartInterface;
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
         * @var StockableInterface $purchasable
         */
        $purchasable = $addCartItemCommand->getPurchasable();

        $isStockSufficient = $this->availabilityChecker->isStockSufficient(
            $purchasable,
            $addCartItemCommand->getQuantity() + $this->getExistingCartItemQuantityFromCart($addCartItemCommand->getCart(), $purchasable)
        );

        if (!$isStockSufficient) {
            $this->context->addViolation(
                $constraint->message,
                ['%stockable%' => $purchasable->getInventoryName()]
            );
        }
    }

    /**
     * @param CartInterface        $cart
     * @param PurchasableInterface $purchasable
     * @return int
     */
    private function getExistingCartItemQuantityFromCart(CartInterface $cart, PurchasableInterface $purchasable)
    {
        $cartItem = $cart->getItemForProduct($purchasable);

        if ($cartItem instanceof CartItemInterface) {
            return $cartItem->getQuantity();
        }

        return 0;
    }
}

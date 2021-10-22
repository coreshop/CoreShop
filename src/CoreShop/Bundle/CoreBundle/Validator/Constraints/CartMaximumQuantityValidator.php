<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Validator\Constraints;

use CoreShop\Bundle\CoreBundle\Validator\QuantityValidatorService;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Inventory\Model\StockableInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartMaximumQuantityValidator extends ConstraintValidator
{
    public function __construct(private QuantityValidatorService $quantityValidatorService)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        /*
         * @var OrderInterface      $value
         * @var CartMaximumQuantity $constraint
         */
        Assert::isInstanceOf($value, OrderInterface::class);
        Assert::isInstanceOf($constraint, CartMaximumQuantity::class);

        $higherThenMaximum = false;
        $productsChecked = [];
        $invalidProduct = null;
        $maxLimit = null;

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

            if (!is_numeric($product->getMaximumQuantityToOrder())) {
                continue;
            }

            if ($product->getMaximumQuantityToOrder() <= 0) {
                continue;
            }

            $maxLimit = $product->getMaximumQuantityToOrder();
            $higherThenMaximum = $this->quantityValidatorService->isHigherThenMaxLimit(
                $maxLimit,
                $this->getExistingCartItemQuantityFromCart($value, $cartItem)
            );

            $productsChecked[] = $product->getId();

            if (true === $higherThenMaximum) {
                $invalidProduct = $product;

                break;
            }
        }

        if ($invalidProduct instanceof StockableInterface) {
            $this->context->addViolation(
                $constraint->messageAboveMaximum,
                [
                    '%stockable%' => $invalidProduct->getInventoryName(),
                    '%limit%' => $maxLimit,
                ]
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

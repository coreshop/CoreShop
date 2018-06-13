<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Product\Rule\Condition;

use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Webmozart\Assert\Assert;

final class QuantityConditionChecker implements ConditionCheckerInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @param CartContextInterface $cartContext
     */
    public function __construct(CartContextInterface $cartContext)
    {
        $this->cartContext = $cartContext;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(ResourceInterface $subject, RuleInterface $rule, array $configuration, $params = [])
    {
        /**
         * @var $subject ProductInterface
         */
        Assert::isInstanceOf($subject, ProductInterface::class);

        $cart = $this->cartContext->getCart();

        foreach ($cart->getItems() as $item) {
            if ($item instanceof CartItemInterface) {
                if ($item->getProduct() instanceof ProductInterface) {
                    if ($item->getProduct()->getId() === $subject->getId()) {
                        return $item->getQuantity() >= $configuration['minQuantity'] && $item->getQuantity() <= $configuration['maxQuantity'];
                    }
                }
            }
        }

        return false;
    }
}

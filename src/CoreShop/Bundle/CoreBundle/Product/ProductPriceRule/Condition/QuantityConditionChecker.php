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

namespace CoreShop\Bundle\CoreBundle\Product\ProductPriceRule\Condition;

use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class QuantityConditionChecker implements ConditionCheckerInterface
{
    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @param CartManagerInterface $cartManager
     */
    public function __construct(CartManagerInterface $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        /*
         * @var $subject ProductInterface
         */
        Assert::isInstanceOf($subject, ProductInterface::class);

        $cart = $this->cartManager->getCart();

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

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

namespace CoreShop\Bundle\CoreBundle\Cart\Rule\Action;

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;

class FreeShippingActionProcessor implements CartPriceRuleActionProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function applyRule(CartInterface $cart, array $configuration)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function unApplyRule(CartInterface $cart, array $configuration)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(CartInterface $cart, $withTax, array $configuration)
    {
        return 0;
    }
}

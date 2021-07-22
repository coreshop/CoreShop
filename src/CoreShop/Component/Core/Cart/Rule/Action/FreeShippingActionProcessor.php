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

namespace CoreShop\Component\Core\Cart\Rule\Action;

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;

final class FreeShippingActionProcessor implements CartPriceRuleActionProcessorInterface
{
    /**
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @param AdjustmentFactoryInterface $adjustmentFactory
     */
    public function __construct(AdjustmentFactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function applyRule(CartInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem)
    {
        $shippingAdjustments = $cart->getAdjustments(AdjustmentInterface::SHIPPING);

        // Don't apply FreeShipping Conditions multiple times
        foreach ($shippingAdjustments as $adjustment) {
            if ($adjustment->getLabel() === 'FreeShipping') {
                return true;
            }
        }

        $shippingWithTax = $cart->getAdjustmentsTotal(AdjustmentInterface::SHIPPING, true);
        $shippingWithoutTax = $cart->getAdjustmentsTotal(AdjustmentInterface::SHIPPING, false);

        $cart->addAdjustment($this->adjustmentFactory->createWithData(AdjustmentInterface::SHIPPING, 'FreeShipping', -1 * $shippingWithTax, -1 * $shippingWithoutTax));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function unApplyRule(CartInterface $cart, array $configuration, ProposalCartPriceRuleItemInterface $cartPriceRuleItem)
    {
        return true;
    }
}

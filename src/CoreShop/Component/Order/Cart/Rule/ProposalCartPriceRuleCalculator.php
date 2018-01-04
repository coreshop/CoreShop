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

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;

class ProposalCartPriceRuleCalculator implements ProposalCartPriceRuleCalculatorInterface
{
    /**
     * @var FactoryInterface
     */
    private $cartPriceRuleItemFactory;

    /**
     * @var ServiceRegistryInterface
     */
    private $actionServiceRegistry;

    /**
     * @param FactoryInterface $cartPriceRuleItemFactory
     * @param ServiceRegistryInterface $actionServiceRegistry
     */
    public function __construct(FactoryInterface $cartPriceRuleItemFactory, ServiceRegistryInterface $actionServiceRegistry)
    {
        $this->cartPriceRuleItemFactory = $cartPriceRuleItemFactory;
        $this->actionServiceRegistry = $actionServiceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePriceRule(CartInterface $cart, CartPriceRuleInterface $cartPriceRule, CartPriceRuleVoucherCodeInterface $voucherCode = null)
    {
        $priceRuleItem = null;
        $existingPriceRule = false;

        if ($cart->hasPriceRules()) {
            foreach ($cart->getPriceRuleItems() as $rule) {
                if ($rule instanceof ProposalCartPriceRuleItemInterface) {
                    $cartsRule = $rule->getCartPriceRule();

                    if ($cartsRule instanceof CartPriceRuleInterface) {
                        if ($cartsRule->getId() === $cartPriceRule->getId()) {
                            $priceRuleItem = $rule;
                            $existingPriceRule = true;
                            break;
                        }
                    }
                }
            }
        }

        $discountNet = 0;
        $discountGross = 0;

        foreach ($cartPriceRule->getActions() as $action) {
            if ($action instanceof ActionInterface) {
                $actionCommand = $this->actionServiceRegistry->get($action->getType());

                $actionCommand->applyRule($cart, $action->getConfiguration());

                $discountNet += $actionCommand->getDiscount($cart, false, $action->getConfiguration());
                $discountGross += $actionCommand->getDiscount($cart, true, $action->getConfiguration());
            }
        }

        if (0 === $discountGross) {
            if ($existingPriceRule) {
                $cart->removePriceRule($cartPriceRule);
            }

            return false;
        }

        /**
         * @var ProposalCartPriceRuleItemInterface
         */
        if ($priceRuleItem === null) {
            $priceRuleItem = $this->cartPriceRuleItemFactory->createNew();
        }

        $priceRuleItem->setCartPriceRule($cartPriceRule);
        $priceRuleItem->setDiscount($discountNet, false);
        $priceRuleItem->setDiscount($discountGross, true);

        if ($voucherCode) {
            $priceRuleItem->setVoucherCode($voucherCode->getCode());
        }

        if (!$existingPriceRule) {
            $cart->addPriceRule($priceRuleItem);
        }

        return $priceRuleItem;
    }
}
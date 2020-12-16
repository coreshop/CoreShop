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

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use Webmozart\Assert\Assert;

class ProposalCartPriceRuleCalculator implements ProposalCartPriceRuleCalculatorInterface
{
    private $cartPriceRuleItemFactory;
    private $actionServiceRegistry;

    public function __construct(FactoryInterface $cartPriceRuleItemFactory, ServiceRegistryInterface $actionServiceRegistry)
    {
        $this->cartPriceRuleItemFactory = $cartPriceRuleItemFactory;
        $this->actionServiceRegistry = $actionServiceRegistry;
    }

    public function calculatePriceRule(OrderInterface $cart, CartPriceRuleInterface $cartPriceRule, CartPriceRuleVoucherCodeInterface $voucherCode = null): ?ProposalCartPriceRuleItemInterface
    {
        $priceRuleItem = $cart->getPriceRuleByCartPriceRule($cartPriceRule, $voucherCode);
        $existingPriceRule = null !== $priceRuleItem;
        $result = false;

        if ($priceRuleItem === null) {
            /**
             * @var ProposalCartPriceRuleItemInterface $priceRuleItem
             */
            $priceRuleItem = $this->cartPriceRuleItemFactory->createNew();
        }

        $priceRuleItem->setCartPriceRule($cartPriceRule);

        if ($voucherCode) {
            $priceRuleItem->setVoucherCode($voucherCode->getCode());
        }
        $priceRuleItem->setDiscount(0, true);
        $priceRuleItem->setDiscount(0, false);

        foreach ($cartPriceRule->getActions() as $action) {
            if ($action instanceof ActionInterface) {
                $actionCommand = $this->actionServiceRegistry->get($action->getType());

                Assert::isInstanceOf($actionCommand, CartPriceRuleActionProcessorInterface::class);

                $config = $action->getConfiguration();
                $config['action'] = $action;

                $result |= $actionCommand->applyRule($cart, $config, $priceRuleItem);
            }
        }

        if (!$result) {
            if ($priceRuleItem instanceof ProposalCartPriceRuleItemInterface) {
                $cart->removePriceRule($priceRuleItem);
            }

            return null;
        }

        if (!$existingPriceRule) {
            $cart->addPriceRule($priceRuleItem);
        }

        return $priceRuleItem;
    }
}

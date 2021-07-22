<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Rule\Model\ActionInterface;
use Webmozart\Assert\Assert;

class CartPriceRuleUnProcessor implements CartPriceRuleUnProcessorInterface
{
    private ServiceRegistryInterface $actionServiceRegistry;

    public function __construct(ServiceRegistryInterface $actionServiceRegistry)
    {
        $this->actionServiceRegistry = $actionServiceRegistry;
    }

    public function unProcess(OrderInterface $cart, CartPriceRuleInterface $cartPriceRule, CartPriceRuleVoucherCodeInterface $voucherCode = null): bool
    {
        $priceRuleItem = $cart->getPriceRuleByCartPriceRule($cartPriceRule, $voucherCode);

        if ($priceRuleItem instanceof ProposalCartPriceRuleItemInterface) {
            foreach ($cartPriceRule->getActions() as $action) {
                if ($action instanceof ActionInterface) {
                    $actionCommand = $this->actionServiceRegistry->get($action->getType());

                    Assert::isInstanceOf($actionCommand, CartPriceRuleActionProcessorInterface::class);

                    $config = $action->getConfiguration();
                    $config['action'] = $action;

                    $actionCommand->unApplyRule($cart, $config, $priceRuleItem);
                }
            }

            $cart->removePriceRule($priceRuleItem);

            return true;
        }

        return false;
    }
}

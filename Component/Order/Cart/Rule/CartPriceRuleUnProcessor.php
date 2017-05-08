<?php

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;
use CoreShop\Component\Rule\Model\ActionInterface;

class CartPriceRuleUnProcessor implements CartPriceRuleUnProcessorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    private $actionServiceRegistry;

    /**
     * @param ServiceRegistryInterface $actionServiceRegistry
     */
    public function __construct(
        ServiceRegistryInterface $actionServiceRegistry
    )
    {
        $this->actionServiceRegistry = $actionServiceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function unProcess(CartPriceRuleInterface $cartPriceRule, $usedCode, CartInterface $cart) {
        $priceRuleItem = null;

        if ($cart->hasPriceRules()) {
            foreach ($cart->getPriceRuleItems() as $rule) {
                if ($rule instanceof ProposalCartPriceRuleItemInterface) {
                    $cartsRule = $rule->getCartPriceRule();

                    if ($cartsRule instanceof CartPriceRuleInterface) {
                        if ($cartsRule->getId() === $cartPriceRule->getId() && $usedCode === $rule->getVoucherCode()) {
                            $priceRuleItem = $rule;
                            break;
                        }
                    }
                }
            }
        }
        
        if ($priceRuleItem instanceof ProposalCartPriceRuleItemInterface) {
            foreach ($cartPriceRule->getActions() as $action) {
                if ($action instanceof ActionInterface) {
                    $actionCommand = $this->actionServiceRegistry->get($action->getType());

                    $actionCommand->unApplyRule($cart, $action->getConfiguration());
                }
            }

            $cart->removePriceRule($cartPriceRule);

            //TODO: Shouldn't this do the cart-manager?
            if ($cart->getId()) {
                $cart->save();
            }

            return true;
        }

        return false;
    }
}
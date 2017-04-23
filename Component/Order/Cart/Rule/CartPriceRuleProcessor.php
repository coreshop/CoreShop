<?php

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Rule\Condition\RuleValidationProcessorInterface;

class CartPriceRuleProcessor implements CartPriceRuleProcessorInterface
{
    /**
     * @var RuleValidationProcessorInterface
     */
    private $cartPriceRuleValidator;

    /**
     * @var FactoryInterface
     */
    private $cartPriceRuleItemFactory;

    /**
     * @param RuleValidationProcessorInterface $cartPriceRuleValidator
     * @param FactoryInterface $cartPriceRuleItemFactory
     */
    public function __construct(RuleValidationProcessorInterface $cartPriceRuleValidator, FactoryInterface $cartPriceRuleItemFactory)
    {
        $this->cartPriceRuleValidator = $cartPriceRuleValidator;
        $this->cartPriceRuleItemFactory = $cartPriceRuleItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CartPriceRuleInterface $cartPriceRule, $usedCode, CartInterface $cart) {
        $priceRuleItem = null;
        
        foreach ($cart->getPriceRules() as $rule) {
            if ($rule instanceof ProposalCartPriceRuleItemInterface) {
                $cartPriceRule = $rule->getCartPriceRule();

                if ($cartPriceRule instanceof CartPriceRuleInterface) {
                    if ($cartPriceRule->getId() === $cartPriceRule->getId()) {
                        $priceRuleItem = $rule;
                    }
                }
            }
        }
        
        if ($this->cartPriceRuleValidator->isValid($cart, $cartPriceRule)) {
            $discount = 0;

            foreach ($cartPriceRule->getActions() as $action) {
                if ($action instanceof CartPriceRuleActionProcessorInterface) {
                    $action->applyRule($cart, $action->getConfiguration());

                    $discount += $action->getDiscount($cart, false, $action->getConfiguration());
                }
            }

            /**
             * @var $priceRuleItem ProposalCartPriceRuleItemInterface
             */
            if ($priceRuleItem === null) {
                $priceRuleItem = $this->cartPriceRuleItemFactory->createNew();
            }

            $priceRuleItem->setCartPriceRule($cartPriceRule);
            $priceRuleItem->setVoucherCode($usedCode);
            $priceRuleItem->setDiscount($discount);
            
            $cart->addPriceRule($priceRuleItem);

            //TODO: Shouldn't this do the cart-manager?
            if ($cart->getId()) {
                $cart->save();
            }

            return true;
        }

        return false;
    }
}
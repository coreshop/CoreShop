<?php

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler\RegisterActionConditionPass;

final class CartPriceRuleConditionPass extends RegisterActionConditionPass
{
    protected function getIdentifier()
    {
        return 'coreshop.cart_price_rule.conditions';
    }

    protected function getTagIdentifier()
    {
        return 'coreshop.cart_price_rule.condition';
    }

    protected function getRegistryIdentifier()
    {
        return 'coreshop.registry.cart_price_rule.conditions';
    }

    protected function getFormRegistryIdentifier()
    {
        return 'coreshop.form_registry.cart_price_rule.conditions';
    }
}

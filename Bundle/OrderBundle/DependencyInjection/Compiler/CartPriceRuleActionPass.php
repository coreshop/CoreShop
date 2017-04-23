<?php

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler\RegisterActionConditionPass;

final class CartPriceRuleActionPass extends RegisterActionConditionPass
{
    protected function getIdentifier()
    {
        return 'coreshop.cart_price_rule.actions';
    }

    protected function getTagIdentifier()
    {
        return 'coreshop.cart_price_rule.action';
    }

    protected function getRegistryIdentifier()
    {
        return 'coreshop.registry.cart_price_rule.actions';
    }

    protected function getFormRegistryIdentifier()
    {
        return 'coreshop.form_registry.cart_price_rule.actions';
    }
}

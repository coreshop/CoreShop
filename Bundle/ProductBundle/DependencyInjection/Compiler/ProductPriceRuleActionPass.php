<?php

namespace CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler\RegisterActionConditionPass;

final class ProductPriceRuleActionPass extends RegisterActionConditionPass
{
    protected function getIdentifier()
    {
        return 'coreshop.product_price_rule.action';
    }

    protected function getTagIdentifier()
    {
        return 'coreshop.product_price_rule.action';
    }

    protected function getRegistryIdentifier()
    {
        return 'coreshop.registry.product_price_rule.actions';
    }

    protected function getFormRegistryIdentifier()
    {
        return 'coreshop.form_registry.product_price_rule.actions';
    }

}
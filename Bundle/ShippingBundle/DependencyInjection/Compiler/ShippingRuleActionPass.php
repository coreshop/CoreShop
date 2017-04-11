<?php

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler\RegisterActionConditionPass;

final class ShippingRuleActionPass extends RegisterActionConditionPass
{
    protected function getIdentifier()
    {
        return 'coreshop.shipping_rule.actions';
    }

    protected function getTagIdentifier()
    {
        return 'coreshop.shipping_rule.action';
    }

    protected function getRegistryIdentifier()
    {
        return 'coreshop.registry.shipping_rule.actions';
    }

    protected function getFormRegistryIdentifier()
    {
        return 'coreshop.form_registry.shipping_rule.actions';
    }
}

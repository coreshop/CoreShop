<?php

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler\RegisterActionConditionPass;

final class ShippingRuleConditionPass extends RegisterActionConditionPass
{
    protected function getIdentifier()
    {
        return 'coreshop.shipping_rule.conditions';
    }

    protected function getTagIdentifier()
    {
        return 'coreshop.shipping_rule.condition';
    }

    protected function getRegistryIdentifier()
    {
        return 'coreshop.registry.shipping_rule.conditions';
    }

    protected function getFormRegistryIdentifier()
    {
        return 'coreshop.form_registry.shipping_rule.conditions';
    }
}

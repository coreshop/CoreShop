<?php

namespace CoreShop\Bundle\ShippingBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleType;

final class ShippingRuleType extends RuleType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_shipping_rule';
    }
}

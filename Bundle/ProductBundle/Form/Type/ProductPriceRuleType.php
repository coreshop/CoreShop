<?php

namespace CoreShop\Bundle\ProductBundle\Form\Type;

use CoreShop\Bundle\PromotionBundle\Form\Type\RuleType;

final class ProductPriceRuleType extends RuleType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_price_rule';
    }
}

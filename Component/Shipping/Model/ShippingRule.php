<?php

namespace CoreShop\Component\Shipping\Model;

use CoreShop\Component\Rule\Model\RuleTrait;

class ShippingRule implements ShippingRuleInterface
{
     use RuleTrait;

     /**
     * @var int
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
}
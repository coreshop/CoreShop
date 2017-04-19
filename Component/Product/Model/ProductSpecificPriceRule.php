<?php

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Rule\Model\RuleTrait;

class ProductSpecificPriceRule implements ProductSpecificPriceRuleInterface
{
    use RuleTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $product;

    /**
     * @var bool
     */
    protected $inherit = false;

    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct($product)
    {
        $this->product = $product;
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInherit()
    {
        return $this->inherit;
    }

    /**
     * {@inheritdoc}
     */
    public function setInherit($inherit)
    {
        $this->inherit = $inherit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }
}

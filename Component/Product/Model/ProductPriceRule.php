<?php

namespace CoreShop\Component\Product\Model;

use CoreShop\Component\Rule\Model\AbstractRule;
use CoreShop\Component\Rule\Model\RuleSubjectInterface;

class ProductPriceRule extends AbstractRule implements ProductPriceRuleInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var boolean
     */
    protected $active;

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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }
}
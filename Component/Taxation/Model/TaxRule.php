<?php

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\AbstractResource;

/**
 * Class TaxRule.
 */
class TaxRule extends AbstractResource implements TaxRuleInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var TaxRuleGroupInterface
     */
    protected $taxRuleGroup;

    /**
     * @var TaxRateInterface
     */
    protected $taxRate;

    /**
     * @var int
     */
    protected $behavior;

    /**
     * @return string
     */
    public function __toString()
    {
        $tax = $this->getTaxRate() instanceof TaxRateInterface ? $this->getTaxRate()->getName() : 'none';

        return sprintf('%s (%s)', $tax, $this->getId());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getBehavior()
    {
        return $this->behavior;
    }

    /**
     * {@inheritdoc}
     */
    public function setBehavior($behavior)
    {
        $this->behavior = $behavior;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRuleGroup()
    {
        return $this->taxRuleGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRuleGroup(TaxRuleGroupInterface $taxRuleGroup)
    {
        $this->taxRuleGroup = $taxRuleGroup;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxRate(TaxRateInterface $taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }
}

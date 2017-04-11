<?php

namespace CoreShop\Component\Shipping\Model;

use CoreShop\Component\Resource\Model\SetValuesTrait;

class ShippingRuleGroup implements ShippingRuleGroupInterface
{
    use SetValuesTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var CarrierInterface
     */
    private $carrier;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var ShippingRuleInterface
     */
    private $shippingRule;

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
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * {@inheritdoc}
     */
    public function setCarrier(CarrierInterface $carrier)
    {
        $this->carrier = $carrier;
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
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingRule()
    {
        return $this->shippingRule;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingRule(ShippingRuleInterface $shippingRule)
    {
        $this->shippingRule = $shippingRule;
    }
}
<?php

namespace CoreShop\Component\Shipping\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ShippingRuleGroupInterface extends ResourceInterface {

    /**
     * @return CarrierInterface
     */
    public function getCarrier();

    /**
     * @param CarrierInterface $carrier
     */
    public function setCarrier(CarrierInterface $carrier);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     */
    public function setPriority($priority);

    /**
     * @return ShippingRuleInterface
     */
    public function getShippingRule();

    /**
     * @param ShippingRuleInterface $shippingRule
     */
    public function setShippingRule(ShippingRuleInterface $shippingRule);
}
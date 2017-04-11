<?php

namespace CoreShop\Component\Shipping\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface CarrierInterface extends ResourceInterface {
    /**
     * Range Behaviour Deactivate
     */
    const RANGE_BEHAVIOUR_DEACTIVATE = 'deactivate';

    /**
     * Range Behaviour Largest
     */
    const RANGE_BEHAVIOUR_LARGEST = 'largest';

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getTrackingUrl();

    /**
     * @param string $trackingUrl
     */
    public function setTrackingUrl($trackingUrl);

    /**
     * @return bool
     */
    public function getIsFree();

    /**
     * @param bool $isFree
     */
    public function setIsFree($isFree);

    /**
     * @return int
     */
    public function getRangeBehaviour();

    /**
     * @param int $rangeBehaviour
     */
    public function setRangeBehaviour($rangeBehaviour);

    /**
     * @return Collection|ShippingRuleGroupInterface[]
     */
    public function getShippingRules();

    /**
     * @return bool
     */
    public function hasShippingRules();

    /**
     * @param ShippingRuleGroupInterface $shippingRuleGroup
     */
    public function addShippingRule(ShippingRuleGroupInterface $shippingRuleGroup);

    /**
     * @param ShippingRuleGroupInterface $shippingRuleGroup
     */
    public function removeShippingRule(ShippingRuleGroupInterface $shippingRuleGroup);

    /**
     * @param ShippingRuleGroupInterface $shippingRuleGroup
     *
     * @return bool
     */
    public function hasShippingRule(ShippingRuleGroupInterface $shippingRuleGroup);
}
<?php

namespace CoreShop\Bundle\ShippingBundle\Processor;

use CoreShop\Component\Shipping\Rule\Action\CarrierPriceActionProcessorInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;

class ShippingRuleActionProcessor implements ShippingRuleActionProcessorInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $actionServiceRegistry;

    /**
     * @param $actionServiceRegistry
     */
    public function __construct($actionServiceRegistry)
    {
        $this->actionServiceRegistry = $actionServiceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(ShippingRuleInterface $shippingRule, CarrierInterface $carrier, AddressInterface $address, $withTax = true)
    {
        $price = 0;

        foreach ($shippingRule->getActions() as $action) {
            $processor = $this->actionServiceRegistry->get($action->getType());

            if ($processor instanceof CarrierPriceActionProcessorInterface) {
                $price += $processor->getPrice($carrier, $address, $action->getConfiguration(), $withTax);
            }
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getModification(ShippingRuleInterface $shippingRule, CarrierInterface $carrier, AddressInterface $address, $price)
    {
        $modifications = 0;

        foreach ($shippingRule->getActions() as $action) {
            $processor = $this->actionServiceRegistry->get($action->getType());

            if ($processor instanceof CarrierPriceActionProcessorInterface) {
                $modifications += $processor->getModification($carrier, $address, $price, $action->getConfiguration());
            }
        }

        return $modifications;
    }
}
<?php

namespace CoreShop\Component\Shipping\Rule\Action;

use CoreShop\Bundle\ShippingBundle\Processor\ShippingRuleActionProcessorInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleInterface;

class ShippingRuleActionProcessor implements CarrierPriceActionProcessorInterface
{
    /**
     * @var ShippingRuleActionProcessorInterface
     */
    protected $shippingRuleProcessor;

    /**
     * @var RepositoryInterface
     */
    protected $shippingRuleRepository;

    /**
     * @param ShippingRuleActionProcessorInterface $shippingRuleProcessor
     * @param RepositoryInterface $shippingRuleRepository
     */
    public function __construct(ShippingRuleActionProcessorInterface $shippingRuleProcessor, RepositoryInterface $shippingRuleRepository)
    {
        $this->shippingRuleProcessor = $shippingRuleProcessor;
        $this->shippingRuleRepository = $shippingRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(CarrierInterface $carrier, AddressInterface $address, array $configuration, $withTax = true)
    {
        $shippingRule = $this->shippingRuleRepository->find($configuration['shippingRule']);

        if ($shippingRule instanceof ShippingRuleInterface) {
            return $this->shippingRuleProcessor->getPrice($shippingRule, $carrier, $address, $withTax);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getModification(CarrierInterface $carrier, AddressInterface $address, $price, array $configuration)
    {
        $shippingRule = $this->shippingRuleRepository->find($configuration['shippingRule']);

        if ($shippingRule instanceof ShippingRuleInterface) {
            return $this->shippingRuleProcessor->getModification($shippingRule, $carrier, $address, $price);
        }
        
        return 0;
    }
}
<?php

namespace CoreShop\Bundle\ShippingBundle\Processor;

use CoreShop\Bundle\ShippingBundle\Checker\CarrierShippingRuleCheckerInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Shipping\Model\ShippingRuleGroupInterface;

class CartCarrierProcessor implements  CartCarrierProcessorInterface
{
    /**
     * @var RepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var CarrierShippingRuleCheckerInterface
     */
    private $carrierShippingRuleChecker;

    /**
     * @param RepositoryInterface $carrierRepository
     * @param CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker
     */
    public function __construct(
        RepositoryInterface $carrierRepository,
        CarrierShippingRuleCheckerInterface $carrierShippingRuleChecker
    )
    {
        $this->carrierRepository = $carrierRepository;
        $this->carrierShippingRuleChecker = $carrierShippingRuleChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getCarriersForCart(CartInterface $cart, AddressInterface $address = null)
    {
        $carriers = $this->carrierRepository->findAll(); //TODO: restrict to store, but needs to be moved to CoreBundle to make it work
        $availableCarriers = [];

        //First: Get all available carriers
        foreach ($carriers as $carrier) {
            if ($this->carrierShippingRuleChecker->isShippingRuleValid($carrier, $cart, $address) instanceof ShippingRuleGroupInterface) {
                $availableCarriers[] = $carrier;
            }
        }

        return $availableCarriers;
    }
}
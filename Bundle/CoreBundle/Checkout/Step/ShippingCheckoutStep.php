<?php

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\ShippingBundle\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Bundle\ShippingBundle\Processor\CartCarrierProcessorInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

class ShippingCheckoutStep implements CheckoutStepInterface
{
    /**
     * @var CartCarrierProcessorInterface
     */
    private $cartCarrierProcessor;

    /**
     * @var CarrierPriceCalculatorInterface
     */
    private $carrierPriceCalculator;

    /**
     * @param CartCarrierProcessorInterface $cartCarrierProcessor
     * @param CarrierPriceCalculatorInterface $carrierPriceCalculator
     */
    public function __construct(CartCarrierProcessorInterface $cartCarrierProcessor, CarrierPriceCalculatorInterface $carrierPriceCalculator)
    {
        $this->cartCarrierProcessor = $cartCarrierProcessor;
        $this->carrierPriceCalculator = $carrierPriceCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'shipping';
    }

    /**
     * {@inheritdoc}
     */
    public function doAutoForward()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        return $cart->getCarrier() instanceof CarrierInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        //TODO: Implement Shipping/Carrier Form Type, validate here and apply carrier to cart
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        //Get Carriers
        $carriers = $this->cartCarrierProcessor->getCarriersForCart($cart, $cart->getShippingAddress());
        $availableCarriers = [];

        foreach ($carriers as $carrier) {
            $carrierPrice = $this->carrierPriceCalculator->getPrice($carrier, $cart, $cart->getShippingAddress());

            $availableCarriers[$carrier->getId()] = [
                'carrier' => $carrier,
                'price' => $carrierPrice
            ];
        }

        //TODO: Create Form?!
        return [
            'carriers' => $availableCarriers
        ];
    }
}
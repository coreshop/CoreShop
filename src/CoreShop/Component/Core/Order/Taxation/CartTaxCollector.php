<?php

namespace CoreShop\Component\Core\Order\Taxation;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Taxation\TaxCalculatorFactoryInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Taxation\ProposalTaxCollectorInterface;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;
use Webmozart\Assert\Assert;

class CartTaxCollector implements ProposalTaxCollectorInterface
{
    /**
     * @var ProposalTaxCollectorInterface
     */
    private $cartTaxCollectorInner;

    /**
     * @var TaxCollectorInterface
     */
    private $taxCollector;

    /**
     * @var TaxCalculatorFactoryInterface
     */
    private $taxCalculatorFactory;

    /**
     * @param TaxCollectorInterface $taxCollector
     * @param TaxCalculatorFactoryInterface $taxCalculatorFactory
     * @param ProposalTaxCollectorInterface $cartTaxCollectorInner
     */
    public function __construct(
        TaxCollectorInterface $taxCollector,
        TaxCalculatorFactoryInterface $taxCalculatorFactory,
        ProposalTaxCollectorInterface $cartTaxCollectorInner
    )
    {
        $this->taxCollector = $taxCollector;
        $this->taxCalculatorFactory = $taxCalculatorFactory;
        $this->cartTaxCollectorInner = $cartTaxCollectorInner;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxes(ProposalInterface $cart)
    {
        /**
         * @var $cart CartInterface
         */
        Assert::isInstanceOf($cart, CartInterface::class);

        $usedTaxes = $this->cartTaxCollectorInner->getTaxes($cart);

        $carrier = $cart->getCarrier();

        if ($carrier instanceof CarrierInterface) {
            $carrierTaxRule = $carrier->getTaxRule();
            $carrierTaxCalculator = $this->taxCalculatorFactory->getTaxCalculatorForAddress($carrierTaxRule, $cart->getShippingAddress());

            $usedTaxes = $this->taxCollector->mergeTaxes($this->taxCollector->collectTaxes($carrierTaxCalculator, $cart->getShipping(false)), $usedTaxes);
        }

        return $usedTaxes;
    }
}
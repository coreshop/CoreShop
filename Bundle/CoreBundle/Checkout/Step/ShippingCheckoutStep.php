<?php

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\CarrierType;
use CoreShop\Bundle\CurrencyBundle\Formatter\MoneyFormatterInterface;
use CoreShop\Bundle\ShippingBundle\Calculator\CarrierPriceCalculatorInterface;
use CoreShop\Bundle\ShippingBundle\Processor\CartCarrierProcessorInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Valid;

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
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @param CartCarrierProcessorInterface $cartCarrierProcessor
     * @param CarrierPriceCalculatorInterface $carrierPriceCalculator
     * @param FormFactoryInterface $formFactory
     * @param CurrencyContextInterface $currencyContext
     * @param MoneyFormatterInterface $moneyFormatter
     */
    public function __construct(
        CartCarrierProcessorInterface $cartCarrierProcessor,
        CarrierPriceCalculatorInterface $carrierPriceCalculator,
        FormFactoryInterface $formFactory,
        CurrencyContextInterface $currencyContext,
        MoneyFormatterInterface $moneyFormatter
    )
    {
        $this->cartCarrierProcessor = $cartCarrierProcessor;
        $this->carrierPriceCalculator = $carrierPriceCalculator;
        $this->formFactory = $formFactory;
        $this->currencyContext = $currencyContext;
        $this->moneyFormatter = $moneyFormatter;
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

            $availableCarriers[$carrier->getId()] = new \stdClass();
            $availableCarriers[$carrier->getId()]->carrier = $carrier;
            $availableCarriers[$carrier->getId()]->price = $carrierPrice;
        }

        $form = $this->formFactory->createNamed('', FormType::class);

        $form->add('carrier', ChoiceType::class, [
            'constraints' => [new Valid()],
            'choices' => $availableCarriers,
            'expanded' => true,
            'choice_label' => function($carrier) {
                return $carrier->carrier->getLabel() . " " . $this->moneyFormatter->format($carrier->price, $this->currencyContext->getCurrency()->getIsoCode());
            }
        ]);

        return [
            'carriers' => $availableCarriers,
            'form' => $form->createView()
        ];
    }
}
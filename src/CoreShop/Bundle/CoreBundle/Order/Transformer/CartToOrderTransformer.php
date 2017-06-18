<?php

namespace CoreShop\Bundle\CoreBundle\Order\Transformer;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use Webmozart\Assert\Assert;

final class CartToOrderTransformer implements ProposalTransformerInterface
{
    /**
     * @var ProposalTransformerInterface
     */
    protected $innerCartToOrderTransformer;
    
    /**
     * @var CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @param ProposalTransformerInterface $innerCartToOrderTransformer
     * @param CurrencyConverterInterface $currencyConverter
     */
    public function __construct(
        ProposalTransformerInterface $innerCartToOrderTransformer,
        CurrencyConverterInterface $currencyConverter
    )
    {
        $this->innerCartToOrderTransformer = $innerCartToOrderTransformer;
        $this->currencyConverter = $currencyConverter;
    }

    public function transform(ProposalInterface $cart, ProposalInterface $order)
    {
         /**
         * @var $cart CartInterface
         */
        Assert::isInstanceOf($cart, CartInterface::class);
        Assert::isInstanceOf($order, OrderInterface::class);
        
        $order = $this->innerCartToOrderTransformer->transform($cart, $order);
        
        $fromCurrency = $order->getBaseCurrency()->getIsoCode();
        $toCurrency = $order->getCurrency()->getIsoCode();

        if ($cart->getCarrier() instanceof CarrierInterface) {
            $order->setCarrier($cart->getCarrier());
            $order->setShipping($this->currencyConverter->convert($cart->getShipping(true), $fromCurrency, $toCurrency), true);
            $order->setShipping($this->currencyConverter->convert($cart->getShipping(false), $fromCurrency, $toCurrency), false);
            $order->setShippingTaxRate($cart->getShippingTaxRate());
            $order->setShippingTax($this->currencyConverter->convert($order->getShipping(true) - $order->getShipping(false), $fromCurrency, $toCurrency));

            $order->setBaseShipping($cart->getShipping(true), true);
            $order->setBaseShipping($cart->getShipping(false), false);
            $order->setBaseShippingTax($order->getShipping(true) - $order->getShipping(false));
        } else {
            $order->setShipping(0, true);
            $order->setShipping(0, false);
            $order->setShippingTax(0);
            $order->setShippingTaxRate(0);

            $order->setBaseShipping(0, true);
            $order->setBaseShipping(0, false);
            $order->setBaseShippingTax(0);
        }

        $order->save();

        return $order;
    }
}
<?php

namespace CoreShop\Component\Core\Order\Transformer;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\QuoteInterface;
use CoreShop\Component\Core\OrderPaymentStates;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\OrderInvoiceStates;
use CoreShop\Component\Order\OrderShippingStates;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use Webmozart\Assert\Assert;

final class CartToSaleTransformer implements ProposalTransformerInterface
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

    /**
     * @param ProposalInterface $cart
     * @param ProposalInterface $sale
     * @return ProposalInterface|mixed
     */
    public function transform(ProposalInterface $cart, ProposalInterface $sale)
    {
         /**
         * @var $cart CartInterface
         */
        Assert::isInstanceOf($cart, CartInterface::class);
        Assert::isInstanceOf($sale, SaleInterface::class);
        
        $sale = $this->innerCartToOrderTransformer->transform($cart, $sale);
        
        $fromCurrency = $sale->getBaseCurrency()->getIsoCode();
        $toCurrency = $sale->getCurrency()->getIsoCode();

        if($sale instanceof OrderInterface) {
            $sale->setOrderState(OrderStates::STATE_INITIALIZED);
            $sale->setShippingState(OrderShippingStates::STATE_NEW);
            $sale->setPaymentState(OrderPaymentStates::STATE_NEW);
            $sale->setInvoiceState(OrderInvoiceStates::STATE_NEW);
        }

        if ($sale instanceof QuoteInterface || $sale instanceof OrderInterface) {
            if ($cart->getCarrier() instanceof CarrierInterface) {
                $sale->setCarrier($cart->getCarrier());
                $sale->setComment($cart->getComment());
                $sale->setAdditionalData($cart->getAdditionalData());
                $sale->setShipping($this->currencyConverter->convert($cart->getShipping(true), $fromCurrency, $toCurrency), true);
                $sale->setShipping($this->currencyConverter->convert($cart->getShipping(false), $fromCurrency, $toCurrency), false);
                $sale->setShippingTaxRate($cart->getShippingTaxRate());
                $sale->setShippingTax($sale->getShipping(true) - $sale->getShipping(false));

                $sale->setBaseShipping($cart->getShipping(true), true);
                $sale->setBaseShipping($cart->getShipping(false), false);
                $sale->setBaseShippingTax($cart->getShipping(true) - $cart->getShipping(false));
            } else {
                $sale->setShipping(0, true);
                $sale->setShipping(0, false);
                $sale->setShippingTax(0);
                $sale->setShippingTaxRate(0);

                $sale->setBaseShipping(0, true);
                $sale->setBaseShipping(0, false);
                $sale->setBaseShippingTax(0);
            }

            $sale->save();
        }

        return $sale;
    }
}
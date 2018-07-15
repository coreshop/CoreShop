<?php

namespace CoreShop\Component\Core\Order\Transformer;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\QuoteInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use CoreShop\Component\Payment\Model\PaymentSettingsAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
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
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param ProposalTransformerInterface $innerCartToOrderTransformer
     * @param CurrencyConverterInterface $currencyConverter
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ProposalTransformerInterface $innerCartToOrderTransformer,
        CurrencyConverterInterface $currencyConverter,
        ObjectManager $objectManager
    )
    {
        $this->innerCartToOrderTransformer = $innerCartToOrderTransformer;
        $this->currencyConverter = $currencyConverter;
        $this->objectManager = $objectManager;
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

        if ($sale instanceof QuoteInterface || $sale instanceof OrderInterface) {
            if ($cart->getCarrier() instanceof CarrierInterface) {
                $sale->setCarrier($cart->getCarrier());
                $sale->setComment($cart->getComment());

                if ($sale instanceof PaymentSettingsAwareInterface) {
                    $sale->setPaymentSettings($cart->getPaymentSettings());
                }
                
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

            $this->objectManager->persist($sale);
            $this->objectManager->flush();
        }

        return $sale;
    }
}
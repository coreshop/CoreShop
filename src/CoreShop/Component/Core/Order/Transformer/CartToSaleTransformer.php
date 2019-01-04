<?php

namespace CoreShop\Component\Core\Order\Transformer;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\QuoteInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use CoreShop\Component\Payment\Model\PaymentSettingsAwareInterface;
use Webmozart\Assert\Assert;

final class CartToSaleTransformer implements ProposalTransformerInterface
{
    /**
     * @var ProposalTransformerInterface
     */
    private $innerCartToOrderTransformer;

    /**
     * @param ProposalTransformerInterface $innerCartToOrderTransformer
     */
    public function __construct(
        ProposalTransformerInterface $innerCartToOrderTransformer
    ) {
        $this->innerCartToOrderTransformer = $innerCartToOrderTransformer;
    }

    /**
     * @param ProposalInterface $cart
     * @param ProposalInterface $sale
     *
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

        if ($sale instanceof QuoteInterface || $sale instanceof OrderInterface) {
            if ($cart->getCarrier() instanceof CarrierInterface) {
                $sale->setCarrier($cart->getCarrier());
                $sale->setComment($cart->getComment());

                if ($sale instanceof PaymentSettingsAwareInterface) {
                    $sale->setPaymentSettings($cart->getPaymentSettings());
                }

                $sale->setShippingTaxRate($cart->getShippingTaxRate());
            } else {
                $sale->setShippingTaxRate(0);
            }

            $sale->setAdditionalData($cart->getAdditionalData());
            $sale->save();
        }

        return $sale;
    }
}

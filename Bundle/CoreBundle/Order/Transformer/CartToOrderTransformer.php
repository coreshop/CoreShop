<?php

namespace CoreShop\Bundle\CoreBundle\Order\Transformer;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
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
     * @param ProposalTransformerInterface $innerCartToOrderTransformer
     */
    public function __construct(ProposalTransformerInterface $innerCartToOrderTransformer)
    {
        $this->innerCartToOrderTransformer = $innerCartToOrderTransformer;
    }

    public function transform(ProposalInterface $cart, ProposalInterface $order)
    {
         /**
         * @var $cart CartInterface
         */
        Assert::isInstanceOf($cart, CartInterface::class);
        Assert::isInstanceOf($order, OrderInterface::class);

        $order = $this->innerCartToOrderTransformer->transform($cart, $order);

        if ($cart->getCarrier() instanceof CarrierInterface) {
            $order->setCarrier($cart->getCarrier());
            $order->setShipping($cart->getShipping(true), true);
            $order->setShipping($cart->getShipping(false), false);
            $order->setShippingTaxRate($cart->getShippingTaxRate());
            $order->setShippingTax($order->getShipping(true) - $order->getShipping(false));
        } else {
            $order->setShipping(0, true);
            $order->setShipping(0, false);
            $order->setShippingTaxRate(0);
            $order->setShippingTax(0);
        }

        $order->save();

        return $order;
    }
}
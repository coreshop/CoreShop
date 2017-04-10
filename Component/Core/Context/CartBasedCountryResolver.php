<?php

namespace CoreShop\Component\Core\Context;

use CoreShop\Component\Address\Context\RequestBased\RequestResolverInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

final class CartBasedCountryResolver implements RequestResolverInterface
{
    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    public function __construct(CartManagerInterface $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * {@inheritdoc}
     */
    public function findCountry(Request $request)
    {
        $cart = $this->cartManager->getCart();

        if ($cart instanceof CartInterface) {
            if ($cart->getShippingAddress() instanceof AddressInterface) {
                return $cart->getShippingAddress()->getCountry();
            }

            return $cart->getInvoiceAddress()->getCountry();
        }

        return null;
    }
}

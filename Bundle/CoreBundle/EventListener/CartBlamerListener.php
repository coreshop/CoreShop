<?php

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class CartBlamerListener
{
    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @param CartManagerInterface $cartManager
     */
    public function __construct(CartManagerInterface $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * @param InteractiveLoginEvent $interactiveLoginEvent
     */
    public function onInteractiveLogin(InteractiveLoginEvent $interactiveLoginEvent)
    {
        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();
        if (!$user instanceof CustomerInterface) {
            return;
        }

        $this->blame($user);
    }

    /**
     * @param CustomerInterface $user
     */
    private function blame(CustomerInterface $user)
    {
        $cart = $this->getCart();

        if (null === $cart) {
            return;
        }

        $cart->setCustomer($user);

        $this->cartManager->persistCart($cart);
    }

    /**
     * @return CartInterface
     */
    private function getCart()
    {
        return $this->cartManager->getCart();
    }
}

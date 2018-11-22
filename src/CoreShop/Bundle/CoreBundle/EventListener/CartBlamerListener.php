<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
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
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @param CartManagerInterface $cartManager
     * @param CartContextInterface $cartContext
     */
    public function __construct(CartManagerInterface $cartManager, CartContextInterface $cartContext)
    {
        $this->cartManager = $cartManager;
        $this->cartContext = $cartContext;
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
     * @param CustomerRegistrationEvent $event
     */
    public function onRegisterEvent(CustomerRegistrationEvent $event)
    {
        $user = $event->getCustomer();

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

        if (null === $cart->getShippingAddress()) {
            $cart->setShippingAddress($user->getDefaultAddress());
        }

        if (null === $cart->getInvoiceAddress()) {
            $cart->setInvoiceAddress($user->getDefaultAddress());
        }

        $this->cartManager->persistCart($cart);
    }

    /**
     * @return CartInterface
     */
    private function getCart()
    {
        return $this->cartContext->getCart();
    }
}

<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;
use CoreShop\Bundle\CoreBundle\Provider\CustomerCartProviderInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class CartBlamerListener
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var CartManagerInterface
     */
    protected $cartManager;

    /**
     * @var CartContextInterface
     */
    protected $cartContext;

    /**
     * @var CustomerCartProviderInterface
     */
    protected $customerCartProvider;

    /**
     * @var string
     */
    protected $sessionKeyName;

    /**
     * @param RequestStack                  $requestStack
     * @param CartManagerInterface          $cartManager
     * @param CartContextInterface          $cartContext
     * @param CustomerCartProviderInterface $customerCartProvider
     * @param string                        $sessionKeyName
     */
    public function __construct(
        RequestStack $requestStack,
        CartManagerInterface $cartManager,
        CartContextInterface $cartContext,
        CustomerCartProviderInterface $customerCartProvider,
        string $sessionKeyName
    ) {
        $this->requestStack = $requestStack;
        $this->cartManager = $cartManager;
        $this->cartContext = $cartContext;
        $this->customerCartProvider = $customerCartProvider;
        $this->sessionKeyName = $sessionKeyName;
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
    protected function blame(CustomerInterface $user)
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
    protected function getCart()
    {
        $contextCart = $this->cartContext->getCart();
        if ($contextCart instanceof CartInterface && !empty($contextCart->getId())) {
            return $contextCart;
        }

        $customerCart = $this->customerCartProvider->provide();
        if ($customerCart instanceof CartInterface) {
            $this->injectCustomerCartToSession($customerCart);
        }

        return $this->cartContext->getCart();
    }

    /**
     * @param CartInterface $cart
     */
    protected function injectCustomerCartToSession(CartInterface $cart)
    {
        if (!$this->requestStack->getMasterRequest()) {
            return;
        }

        $session = $this->requestStack->getMasterRequest()->getSession();
        if (!$session instanceof SessionInterface) {
            return;
        }

        $session->set(
            sprintf('%s.%s', $this->sessionKeyName, $cart->getStore()->getId()),
            $cart->getId()
        );
    }
}

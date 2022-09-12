<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Bundle\CoreBundle\Event\CustomerRegistrationEvent;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\UserInterface;
use CoreShop\Component\Order\Context\CartContextInterface;
use CoreShop\Component\Order\Context\CartNotFoundException;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class CartBlamerListener
{
    public function __construct(
        private CartProcessorInterface $cartProcessor,
        private CartContextInterface $cartContext,
        private CartManagerInterface $cartManager,
    ) {
    }

    public function onInteractiveLogin(InteractiveLoginEvent $interactiveLoginEvent): void
    {
        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }

        $customer = $user->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return;
        }

        $this->blame($customer);
    }

    public function onRegisterEvent(CustomerRegistrationEvent $event): void
    {
        $user = $event->getCustomer();

        $this->blame($user);
    }

    private function blame(CustomerInterface $user): void
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

        $this->cartProcessor->process($cart);

        if ($cart->getId()) {
            $this->cartManager->persistCart($cart);

            return;
        }

        $this->cartProcessor->process($cart);
    }

    private function getCart(): ?OrderInterface
    {
        try {
            return $this->cartContext->getCart();
        } catch (CartNotFoundException) {
            return null;
        }
    }
}

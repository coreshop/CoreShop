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

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Customer\CustomerManagerInterface;
use CoreShop\Bundle\CoreBundle\Form\Type\GuestRegistrationType;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerCheckoutStep implements CheckoutStepInterface, ValidationCheckoutStepInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private CustomerManagerInterface $customerManager,
        private LocaleContextInterface $localeContext,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'customer';
    }

    public function doAutoForward(OrderInterface $cart): bool
    {
        return true;
    }

    public function validate(OrderInterface $cart): bool
    {
        if (!$cart->hasItems()) {
            return false;
        }

        return $cart->getCustomer() instanceof CustomerInterface;
    }

    public function commitStep(OrderInterface $cart, Request $request): bool
    {
        $form = $this->createForm($request, $cart);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer = $form->getData();
            $customer->setLocaleCode($this->localeContext->getLocaleCode());

            $this->customerManager->persistCustomer($customer);

            return true;
        }

        if (!$this->validate($cart)) {
            throw new CheckoutException('no customer found', 'coreshop.ui.error.coreshop_checkout_customer_invalid');
        }

        return true;
    }

    public function prepareStep(OrderInterface $cart, Request $request): array
    {
        return [
            'guestForm' => $this->createForm($request, $cart)->createView(),
        ];
    }

    private function createForm(Request $request, OrderInterface $cart): FormInterface
    {
        $form = $this->formFactory->createNamed('guest', GuestRegistrationType::class, $cart->getCustomer());

        return $form->handleRequest($request);
    }
}

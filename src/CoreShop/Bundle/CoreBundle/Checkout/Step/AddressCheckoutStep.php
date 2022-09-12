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

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

class AddressCheckoutStep implements CheckoutStepInterface, ValidationCheckoutStepInterface
{
    public function __construct(
        protected CustomerAddressCheckoutStep $customerAddressCheckoutStep,
        protected GuestAddressCheckoutStep $guestAddressCheckoutStep,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'address';
    }

    public function doAutoForward(OrderInterface $cart): bool
    {
        return false;
    }

    public function validate(OrderInterface $cart): bool
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        return $cart->hasItems() &&
            ($cart->hasShippableItems() === false || $cart->getShippingAddress() instanceof AddressInterface) &&
            $cart->getInvoiceAddress() instanceof AddressInterface;
    }

    public function commitStep(OrderInterface $cart, Request $request): bool
    {
        $customer = $cart->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new CheckoutException('Customer not set', 'coreshop.ui.error.coreshop_checkout_internal_error');
        }

        $isGuest = null === $customer->getUser();

        return $isGuest ? $this->guestAddressCheckoutStep->commitStep(
            $cart,
            $request,
        ) : $this->customerAddressCheckoutStep->commitStep($cart, $request);
    }

    public function prepareStep(OrderInterface $cart, Request $request): array
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        $customer = $cart->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new CheckoutException('Customer not set', 'coreshop.ui.error.coreshop_checkout_internal_error');
        }

        $isGuest = null === $customer->getUser();

        return array_merge(
            $isGuest ?
                $this->guestAddressCheckoutStep->prepareStep($cart, $request) :
                $this->customerAddressCheckoutStep->prepareStep($cart, $request),
            ['is_guest' => $isGuest],
        );
    }
}

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

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\GuestAddressType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

class GuestAddressCheckoutStep implements CheckoutStepInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private CartManagerInterface $cartManager,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'guest_address';
    }

    public function doAutoForward(OrderInterface $cart): bool
    {
        return false;
    }

    public function commitStep(OrderInterface $cart, Request $request): bool
    {
        $customer = $cart->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new CheckoutException('Customer not set', 'coreshop.ui.error.coreshop_checkout_internal_error');
        }

        if ($cart->getShippingAddress() && $cart->getInvoiceAddress() &&
            $cart->getInvoiceAddress()->getId() === $cart->getShippingAddress()->getId()) {
            /**
             * @var AddressInterface $shippingAddress
             */
            $shippingAddress = Service::cloneMe($cart->getInvoiceAddress());

            $cart->setShippingAddress($shippingAddress);
        }

        $form = $this->createForm($request, $cart, $customer);

        if ($form->isSubmitted() && $form->isValid()) {
            $cart = $form->getData();

            if ($form->get('useInvoiceAsShipping')->getData()) {
                $cart->getInvoiceAddress()->setParent(Service::createFolderByPath($cart . '/addresses'));
                $cart->getInvoiceAddress()->setKey(uniqid());
                $cart->getInvoiceAddress()->setPublished(true);
                $cart->getInvoiceAddress()->save();

                $cart->setShippingAddress($cart->getInvoiceAddress());
            } else {
                foreach ([$cart->getInvoiceAddress(), $cart->getShippingAddress()] as $address) {
                    $address->setParent(Service::createFolderByPath($cart . '/addresses'));
                    $address->setKey(uniqid());
                    $address->setPublished(true);
                    $address->save();
                }
            }

            $this->cartManager->persistCart($cart);

            return true;
        }

        return false;
    }

    public function prepareStep(OrderInterface $cart, Request $request): array
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        $customer = $cart->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new CheckoutException('Customer not set', 'coreshop.ui.error.coreshop_checkout_internal_error');
        }

        return [
            'form' => $this->createForm($request, $cart, $customer)->createView(),
            'hasShippableItems' => $cart->hasShippableItems(),
        ];
    }

    private function createForm(Request $request, OrderInterface $cart, CustomerInterface $customer): FormInterface
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        $form = $this->formFactory->createNamed('coreshop', GuestAddressType::class, $cart);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);
        }

        return $form;
    }
}

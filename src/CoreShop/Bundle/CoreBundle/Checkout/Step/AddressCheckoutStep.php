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

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\AddressType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddressCheckoutStep implements CheckoutStepInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param FormFactoryInterface $formFactory
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'address';
    }

    /**
     * {@inheritdoc}
     */
    public function doAutoForward()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        return $cart->hasItems() && $cart->getShippingAddress() instanceof AddressInterface && $cart->getInvoiceAddress() instanceof AddressInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        $customer = $this->getCustomer();
        $form = $this->createForm($request, $cart, $customer);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $formData = $form->getData();

                $cart->setShippingAddress($formData['shippingAddress']);
                $cart->setInvoiceAddress($formData['invoiceAddress']);
                $cart->save();

                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart, Request $request)
    {
        $customer = $this->getCustomer();

        return [
            'form' => $this->createForm($request, $cart, $customer)->createView()
        ];
    }

    /**
     * @return CustomerInterface
     *
     * @throws CheckoutException
     */
    private function getCustomer()
    {
        $customer = $this->tokenStorage->getToken()->getUser();

        if (!$customer instanceof CustomerInterface) {
            throw new CheckoutException(sprintf('Customer needs to implement CustomerInterface, %s given', get_class($customer)), 'coreshop.ui.error.coreshop_checkout_internal_error');
        }

        return $customer;
    }

    /**
     * @param Request $request
     * @param CartInterface $cart
     * @param CustomerInterface $customer
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createForm(Request $request, CartInterface $cart, CustomerInterface $customer)
    {
        $customerDefaultAddress = $customer->getDefaultAddress();

        $shippingAddress = $customerDefaultAddress;
        $invoiceAddress = $customerDefaultAddress;

        if ($cart->getShippingAddress() instanceof AddressInterface) {
            $shippingAddress = $cart->getShippingAddress();
        }

        if ($cart->getInvoiceAddress() instanceof AddressInterface) {
            $invoiceAddress = $cart->getInvoiceAddress();
        }

        $values = [
            'shippingAddress' => $shippingAddress,
            'invoiceAddress' => $invoiceAddress,
            'useShippingAsInvoice' => $shippingAddress->getId() === $invoiceAddress->getId()
        ];

        $options = [
            'customer' => $customer->getId(),
        ];

        $form = $this->formFactory->createNamed('', AddressType::class, $values, $options);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);
        }

        return $form;
    }
}

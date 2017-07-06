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
     * @param FormFactoryInterface  $formFactory
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        TokenStorageInterface $tokenStorage
    ) {
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
        $form = $this->createForm($cart, $customer);

        $form->handleRequest($request);
        $formData = $form->getData();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $cart->setShippingAddress($formData['shippingAddress']);
                $cart->setInvoiceAddress($formData['invoiceAddress']);
                $cart->save();

                return true;
            } else {
                throw new CheckoutException('Address Form is invalid', 'coreshop_checkout_address_form_invalid');
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        $customer = $this->getCustomer();

        return [
            'form' => $this->createForm($cart, $customer)->createView(),
            'addresses' => $customer->getAddresses(),
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
            throw new CheckoutException(sprintf('Customer needs to implement CustomerInterface, %s given', get_class($customer)), 'coreshop_checkout_internal_error');
        }

        return $customer;
    }

    /**
     * @param CartInterface     $cart
     * @param CustomerInterface $customer
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createForm(CartInterface $cart, CustomerInterface $customer)
    {
        $addresses = $customer->getAddresses();

        $values = [
            'shippingAddress' => $cart->getShippingAddress() instanceof AddressInterface ? $cart->getShippingAddress()->getId() : (count($addresses) > 0 ? $addresses[0]->getId() : null),
            'invoiceAddress' => $cart->getInvoiceAddress() instanceof AddressInterface ? $cart->getInvoiceAddress()->getId() : (count($addresses) > 0 ? $addresses[0]->getId() : null),
        ];

        $options = [
            'customer' => $customer->getId(),
        ];

        return $this->formFactory->createNamed('', AddressType::class, $values, $options);
    }
}

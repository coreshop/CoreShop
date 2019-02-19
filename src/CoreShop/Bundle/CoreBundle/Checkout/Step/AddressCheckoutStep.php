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

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\AddressType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

class AddressCheckoutStep implements CheckoutStepInterface, ValidationCheckoutStepInterface
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
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @param FormFactoryInterface  $formFactory
     * @param TokenStorageInterface $tokenStorage
     * @param CartManagerInterface  $cartManager
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        TokenStorageInterface $tokenStorage,
        CartManagerInterface $cartManager
    ) {
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
        $this->cartManager = $cartManager;
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
    public function doAutoForward(CartInterface $cart)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\CartInterface::class);

        return $cart->hasItems()
            && ($cart->hasShippableItems() === false || $cart->getShippingAddress() instanceof AddressInterface)
            && $cart->getInvoiceAddress() instanceof AddressInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        $customer = $cart->getCustomer();
        $form = $this->createForm($request, $cart, $customer);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $cart = $form->getData();

                $this->cartManager->persistCart($cart);

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
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\CartInterface::class);

        $customer = $cart->getCustomer();

        return [
            'form' => $this->createForm($request, $cart, $customer)->createView(),
            'hasShippableItems' => $cart->hasShippableItems(),
        ];
    }

    /**
     * @param Request           $request
     * @param CartInterface     $cart
     * @param CustomerInterface $customer
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createForm(Request $request, CartInterface $cart, CustomerInterface $customer)
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\CartInterface::class);

        $options = [
            'customer' => $customer,
        ];

        $form = $this->formFactory->createNamed('', AddressType::class, $cart, $options);

        if ($cart->hasShippableItems() === false) {
            $form->remove('shippingAddress');
            $form->remove('useInvoiceAsShipping');
        }

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);
        }

        return $form;
    }
}

<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\AddressType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

class CustomerAddressCheckoutStep implements CheckoutStepInterface, ValidationCheckoutStepInterface
{
    public function __construct(private FormFactoryInterface $formFactory, private CartManagerInterface $cartManager)
    {
    }

    public function getIdentifier(): string
    {
        return 'customer_address';
    }

    public function doAutoForward(OrderInterface $cart): bool
    {
        return false;
    }

    public function validate(OrderInterface $cart): bool
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        return $cart->hasItems()
            && ($cart->hasShippableItems() === false || $cart->getShippingAddress() instanceof AddressInterface)
            && $cart->getInvoiceAddress() instanceof AddressInterface;
    }

    public function commitStep(OrderInterface $cart, Request $request): bool
    {
        $customer = $cart->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            throw new CheckoutException('Customer not set', 'coreshop.ui.error.coreshop_checkout_internal_error');
        }

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

        $options = [
            'customer' => $customer,
        ];

        $form = $this->formFactory->createNamed('coreshop', AddressType::class, $cart, $options);

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

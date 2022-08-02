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

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\CarrierType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\OptionalCheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

class ShippingCheckoutStep implements CheckoutStepInterface, OptionalCheckoutStepInterface, ValidationCheckoutStepInterface
{
    public function __construct(private ShippableCarrierValidatorInterface $shippableCarrierValidator, private FormFactoryInterface $formFactory, private CartManagerInterface $cartManager)
    {
    }

    public function getIdentifier(): string
    {
        return 'shipping';
    }

    public function isRequired(OrderInterface $cart): bool
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        return $cart->hasShippableItems();
    }

    public function doAutoForward(OrderInterface $cart): bool
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        return $cart->hasShippableItems() === false;
    }

    public function validate(OrderInterface $cart): bool
    {
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        return $cart->hasShippableItems() === false
            || ($cart->hasItems() &&
                $cart->getCarrier() instanceof CarrierInterface &&
                $cart->getShippingAddress() instanceof AddressInterface &&
                $this->shippableCarrierValidator->isCarrierValid($cart->getCarrier(), $cart, $cart->getShippingAddress()));
    }

    public function commitStep(OrderInterface $cart, Request $request): bool
    {
        $form = $this->createForm($request, $cart);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $cart = $form->getData();

                $this->cartManager->persistCart($cart);

                return true;
            }

            throw new CheckoutException('Shipping Form is invalid', 'coreshop.ui.error.coreshop_checkout_shipping_form_invalid');
        }

        return false;
    }

    public function prepareStep(OrderInterface $cart, Request $request): array
    {
        return [
            'form' => $this->createForm($request, $cart)->createView(),
        ];
    }

    private function createForm(Request $request, OrderInterface $cart): FormInterface
    {
        $form = $this->formFactory->createNamed('coreshop', CarrierType::class, $cart, [
            'cart' => $cart,
        ]);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);
        }

        return $form;
    }
}

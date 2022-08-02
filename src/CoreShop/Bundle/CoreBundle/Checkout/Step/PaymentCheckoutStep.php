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

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\PaymentType;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Checkout\OptionalCheckoutStepInterface;
use CoreShop\Component\Order\Checkout\ValidationCheckoutStepInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentCheckoutStep implements CheckoutStepInterface, OptionalCheckoutStepInterface, ValidationCheckoutStepInterface
{
    public function __construct(private FormFactoryInterface $formFactory, private CartManagerInterface $cartManager)
    {
    }

    public function getIdentifier(): string
    {
        return 'payment';
    }

    public function isRequired(OrderInterface $cart): bool
    {
        return $cart->getTotal() > 0;
    }

    public function doAutoForward(OrderInterface $cart): bool
    {
        return false;
    }

    public function validate(OrderInterface $cart): bool
    {
        return $cart->hasItems() && $cart->getPaymentProvider() instanceof PaymentProviderInterface;
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

            throw new CheckoutException('Payment Form is invalid', 'coreshop.ui.error.coreshop_checkout_payment_form_invalid');
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
        $form = $this->formFactory->createNamed('coreshop', PaymentType::class, $cart, [
            'payment_subject' => $cart,
        ]);

        if ($request->isMethod('post')) {
            $form = $form->handleRequest($request);
        }

        return $form;
    }
}

<?php

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\PaymentType;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PaymentCheckoutStep implements CheckoutStepInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'payment';
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
        return $cart->getPaymentProvider() instanceof PaymentProviderInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        $form = $this->createForm($cart);

        $form->handleRequest($request);
        $formData = $form->getData();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $cart->setPaymentProvider($formData['paymentProvider']);
                $cart->save();

                return true;
            }
            else {
                throw new CheckoutException('Payment Form is invalid', 'coreshop_checkout_payment_form_invalid');
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        return [
            'form' => $this->createForm($cart)->createView()
        ];
    }

    /**
     * @param CartInterface $cart
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createForm(CartInterface $cart) {
        $form = $this->formFactory->createNamed('', PaymentType::class, [
            'paymentProvider' => $cart->getPaymentProvider()
        ]);

        return $form;
    }
}
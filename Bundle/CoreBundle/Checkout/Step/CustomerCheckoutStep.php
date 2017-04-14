<?php

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerLoginType;
use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class CustomerCheckoutStep implements CheckoutStepInterface
{
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param AuthorizationChecker $authorizationChecker
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(AuthorizationChecker $authorizationChecker, FormFactoryInterface $formFactory)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'customer';
    }

    /**
     * {@inheritdoc}
     */
    public function doAutoForward()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        return ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY'));
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new CheckoutException("no customer found", 'coreshop_checkout_customer_invalid');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        return [

        ];
    }
}
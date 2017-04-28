<?php

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerLoginType;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\CustomerNotFoundException;
use CoreShop\Component\Customer\Model\CustomerInterface;
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
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param CustomerContextInterface $customerContext
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(CustomerContextInterface $customerContext, FormFactoryInterface $formFactory)
    {
        $this->customerContext = $customerContext;
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
        try {
            $customer = $this->customerContext->getCustomer();

            return $customer instanceof CustomerInterface;
        }
        catch (CustomerNotFoundException $ex) {

        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CartInterface $cart, Request $request)
    {
        if (!$this->validate($cart)) {
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
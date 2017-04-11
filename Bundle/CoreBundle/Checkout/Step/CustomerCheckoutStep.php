<?php

namespace CoreShop\Bundle\CoreBundle\Checkout\Step;

use CoreShop\Component\Order\Checkout\CheckoutException;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class CustomerCheckoutStep implements CheckoutStepInterface
{
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(AuthorizationChecker $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
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
            throw new CheckoutException("no customer found");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CartInterface $cart)
    {
        //Nothing to do here
    }
}
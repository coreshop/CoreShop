<?php

namespace CoreShop\Bundle\CoreBundle\Checkout;

use CoreShop\Component\Order\Checkout\CheckoutManagerInterface;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckoutManager implements CheckoutManagerInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    private $serviceRegistry;

    /**
     * @var array
     */
    private $steps;

    /**
     * @param PrioritizedServiceRegistryInterface $serviceRegistry
     */
    public function __construct(PrioritizedServiceRegistryInterface $serviceRegistry)
    {
        $this->serviceRegistry = $serviceRegistry;
        $this->steps = [];

        foreach ($this->serviceRegistry->all() as $service) {
            if ($service instanceof CheckoutStepInterface) {
                $this->steps[] = $service->getIdentifier();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * {@inheritdoc}
     */
    public function getStep($identifier)
    {
        return $this->serviceRegistry->get($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getNextStep($identifier)
    {
        return $this->serviceRegistry->getNextTo($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousStep($identifier)
    {
        return $this->serviceRegistry->getPreviousTo($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousSteps($identifier) {
        $previousSteps = $this->serviceRegistry->getAllPreviousTo($identifier);

        return is_array($previousSteps) ? $previousSteps : [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateStep(CheckoutStepInterface $step, CartInterface $cart)
    {
        return $step->validate($cart);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CheckoutStepInterface $step, CartInterface $cart)
    {
        return $step->prepareStep($cart);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStep(CartInterface $cart)
    {
        return $this->serviceRegistry->get($cart->getCurrentStep());
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStepIndex($identifier)
    {
        return $this->serviceRegistry->getIndex($identifier) + 1; //Checkout Steps are 1 based
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CheckoutStepInterface $step, CartInterface $cart, Request $request)
    {
        return $step->commitStep($cart, $request);
    }
}
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

namespace CoreShop\Bundle\CoreBundle\Checkout;

use CoreShop\Component\Order\Checkout\CheckoutManagerInterface;
use CoreShop\Component\Order\Checkout\CheckoutStepInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistry;
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

    public function __construct()
    {
        $this->serviceRegistry = new PrioritizedServiceRegistry(CheckoutStepInterface::class, 'checkout-manager');
        $this->steps = [];
    }

    /**
     * {@inheritdoc}
     */
    public function addCheckoutStep(CheckoutStepInterface $step, $priority)
    {
        $this->serviceRegistry->register($step->getIdentifier(), $priority, $step);
        $this->steps[] = $step->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getSteps()
    {
        return array_map(function(CheckoutStepInterface $step) {
            return $step->getIdentifier();
        }, $this->serviceRegistry->all());
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
    public function hasNextStep($identifier)
    {
        return $this->serviceRegistry->hasNextTo($identifier);
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
    public function hasPreviousStep($identifier)
    {
        return $this->serviceRegistry->hasPreviousTo($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousSteps($identifier)
    {
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
    public function prepareStep(CheckoutStepInterface $step, CartInterface $cart, Request $request)
    {
        return $step->prepareStep($cart, $request);
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

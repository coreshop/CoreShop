<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckoutManager implements CheckoutManagerInterface
{
    private $serviceRegistry;
    private $steps;

    public function __construct(PrioritizedServiceRegistryInterface $serviceRegistry)
    {
        $this->serviceRegistry = $serviceRegistry;
        $this->steps = [];
    }

    /**
     * {@inheritdoc}
     */
    public function addCheckoutStep(CheckoutStepInterface $step, int $priority): void
    {
        $this->serviceRegistry->register($step->getIdentifier(), $priority, $step);
        $this->steps[] = $step->getIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getSteps(): array
    {
        return array_map(function (CheckoutStepInterface $step) {
            return $step->getIdentifier();
        }, $this->serviceRegistry->all());
    }

    /**
     * {@inheritdoc}
     */
    public function getStep(string $identifier): CheckoutStepInterface
    {
        /**
         * @var CheckoutStepInterface $step
         */
        $step = $this->serviceRegistry->get($identifier);

        return $step;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextStep(string $identifier): CheckoutStepInterface
    {
        return $this->serviceRegistry->getNextTo($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function hasNextStep(string $identifier): bool
    {
        return $this->serviceRegistry->hasNextTo($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousStep(string $identifier): CheckoutStepInterface
    {
        return $this->serviceRegistry->getPreviousTo($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPreviousStep(string $identifier): bool
    {
        return $this->serviceRegistry->hasPreviousTo($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousSteps(string $identifier): array
    {
        $previousSteps = $this->serviceRegistry->getAllPreviousTo($identifier);

        return is_array($previousSteps) ? $previousSteps : [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateStep(CheckoutStepInterface $step, CartInterface $cart): bool
    {
        return $step->validate($cart);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareStep(CheckoutStepInterface $step, CartInterface $cart, Request $request): array
    {
        return $step->prepareStep($cart, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStepIndex(string $identifier): int
    {
        return $this->serviceRegistry->getIndex($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function commitStep(CheckoutStepInterface $step, CartInterface $cart, Request $request): bool
    {
        return $step->commitStep($cart, $request);
    }
}

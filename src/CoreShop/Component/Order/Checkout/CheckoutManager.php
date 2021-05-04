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

declare(strict_types=1);

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckoutManager implements CheckoutManagerInterface
{
    private PrioritizedServiceRegistryInterface $serviceRegistry;

    public function __construct(PrioritizedServiceRegistryInterface $serviceRegistry)
    {
        $this->serviceRegistry = $serviceRegistry;
    }

    public function addCheckoutStep(CheckoutStepInterface $step, int $priority): void
    {
        $this->serviceRegistry->register($step->getIdentifier(), $priority, $step);
    }

    public function getSteps(): array
    {
        return array_map(function (CheckoutStepInterface $step) {
            return $step->getIdentifier();
        }, $this->serviceRegistry->all());
    }

    public function getStep(string $identifier): CheckoutStepInterface
    {
        /**
         * @var CheckoutStepInterface $step
         */
        $step = $this->serviceRegistry->get($identifier);

        return $step;
    }

    public function getNextStep(string $identifier): CheckoutStepInterface
    {
        return $this->serviceRegistry->getNextTo($identifier);
    }

    public function hasNextStep(string $identifier): bool
    {
        return $this->serviceRegistry->hasNextTo($identifier);
    }

    public function getPreviousStep(string $identifier): CheckoutStepInterface
    {
        return $this->serviceRegistry->getPreviousTo($identifier);
    }

    public function hasPreviousStep(string $identifier): bool
    {
        return $this->serviceRegistry->hasPreviousTo($identifier);
    }

    public function getPreviousSteps(string $identifier): array
    {
        $previousSteps = $this->serviceRegistry->getAllPreviousTo($identifier);

        return is_array($previousSteps) ? $previousSteps : [];
    }

    public function validateStep(CheckoutStepInterface $step, OrderInterface $cart): bool
    {
        if ($step instanceof ValidationCheckoutStepInterface) {
            return $step->validate($cart);
        }

        return true;
    }

    public function prepareStep(CheckoutStepInterface $step, OrderInterface $cart, Request $request): array
    {
        return $step->prepareStep($cart, $request);
    }

    public function getCurrentStepIndex(string $identifier): int
    {
        return $this->serviceRegistry->getIndex($identifier);
    }

    public function commitStep(CheckoutStepInterface $step, OrderInterface $cart, Request $request): bool
    {
        return $step->commitStep($cart, $request);
    }
}

<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

interface CheckoutManagerInterface
{
    public function addCheckoutStep(CheckoutStepInterface $step, int $priority): void;

    /**
     * @return string[]
     */
    public function getSteps(): array;

    public function getStep(string $identifier): ?CheckoutStepInterface;

    public function getNextStep(string $identifier): ?CheckoutStepInterface;

    public function hasNextStep(string $identifier): bool;

    public function getPreviousStep(string $identifier): ?CheckoutStepInterface;

    public function hasPreviousStep(string $identifier): bool;

    /**
     * @return CheckoutStepInterface[]
     */
    public function getPreviousSteps(string $identifier): array;

    public function validateStep(CheckoutStepInterface $step, OrderInterface $cart): bool;

    public function prepareStep(CheckoutStepInterface $step, OrderInterface $cart, Request $request): array;

    public function getCurrentStepIndex(string $identifier): int;

    public function commitStep(CheckoutStepInterface $step, OrderInterface $cart, Request $request): bool;
}

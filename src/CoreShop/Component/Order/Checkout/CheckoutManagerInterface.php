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

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

interface CheckoutManagerInterface
{
    /**
     * @param CheckoutStepInterface $step
     * @param int                   $priority
     */
    public function addCheckoutStep(CheckoutStepInterface $step, int $priority): void;

    /**
     * @return string[]
     */
    public function getSteps(): array;

    /**
     * @param string $identifier
     *
     * @return CheckoutStepInterface|null
     */
    public function getStep(string $identifier): ?CheckoutStepInterface;

    /**
     * @param string $identifier
     *
     * @return CheckoutStepInterface|null
     */
    public function getNextStep(string $identifier): ?CheckoutStepInterface;

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasNextStep(string $identifier): bool;

    /**
     * @param string $identifier
     *
     * @return CheckoutStepInterface|null
     */
    public function getPreviousStep(string $identifier): ?CheckoutStepInterface;

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasPreviousStep(string $identifier): bool;

    /**
     * @param string $identifier
     *
     * @return CheckoutStepInterface[]
     */
    public function getPreviousSteps(string $identifier): array;

    /**
     * @param CheckoutStepInterface $step
     * @param OrderInterface         $cart
     *
     * @return bool
     */
    public function validateStep(CheckoutStepInterface $step, OrderInterface $cart): bool;

    /**
     * @param CheckoutStepInterface $step
     * @param OrderInterface         $cart
     * @param Request               $request
     *
     * @return array
     */
    public function prepareStep(CheckoutStepInterface $step, OrderInterface $cart, Request $request): array;

    /**
     * @param string $identifier
     *
     * @return int
     */
    public function getCurrentStepIndex(string $identifier): int;

    /**
     * @param CheckoutStepInterface $step
     * @param OrderInterface         $cart
     * @param Request               $request
     *
     * @return bool
     */
    public function commitStep(CheckoutStepInterface $step, OrderInterface $cart, Request $request): bool;
}

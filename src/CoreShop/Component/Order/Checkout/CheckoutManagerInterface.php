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
use Symfony\Component\HttpFoundation\Request;

interface CheckoutManagerInterface
{
    /**
     * @param CheckoutStepInterface $step
     * @param int                   $priority
     */
    public function addCheckoutStep(CheckoutStepInterface $step, $priority);

    /**
     * @return string[]
     */
    public function getSteps();

    /**
     * @param string $identifier
     *
     * @return CheckoutStepInterface|null
     */
    public function getStep($identifier);

    /**
     * @param string $identifier
     *
     * @return CheckoutStepInterface|null
     */
    public function getNextStep($identifier);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasNextStep($identifier);

    /**
     * @param string $identifier
     *
     * @return CheckoutStepInterface|null
     */
    public function getPreviousStep($identifier);

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasPreviousStep($identifier);

    /**
     * @param string $identifier
     *
     * @return CheckoutStepInterface[]
     */
    public function getPreviousSteps($identifier);

    /**
     * @param CheckoutStepInterface $step
     * @param CartInterface         $cart
     *
     * @return mixed
     */
    public function validateStep(CheckoutStepInterface $step, CartInterface $cart);

    /**
     * @param CheckoutStepInterface $step
     * @param CartInterface         $cart
     * @param Request               $request
     *
     * @return mixed
     */
    public function prepareStep(CheckoutStepInterface $step, CartInterface $cart, Request $request);

    /**
     * @param string $identifier
     *
     * @return int
     */
    public function getCurrentStepIndex($identifier);

    /**
     * @param CheckoutStepInterface $step
     * @param CartInterface         $cart
     * @param Request               $request
     *
     * @return mixed
     */
    public function commitStep(CheckoutStepInterface $step, CartInterface $cart, Request $request);
}

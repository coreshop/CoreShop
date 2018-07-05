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

namespace CoreShop\Component\Order\Checkout;

use CoreShop\Component\Order\Model\CartInterface;
use Symfony\Component\HttpFoundation\Request;

interface CheckoutManagerInterface
{
    /**
     * @param CheckoutStepInterface $step
     * @param $priority
     */
    public function addCheckoutStep(CheckoutStepInterface $step, $priority);

    /**
     * @return CheckoutStepInterface[]
     */
    public function getSteps();

    /**
     * @param $identifier
     *
     * @return mixed
     */
    public function getStep($identifier);

    /**
     * @param $identifier
     *
     * @return mixed
     */
    public function getNextStep($identifier);

    /**
     * @param $identifier
     *
     * @return bool
     */
    public function hasNextStep($identifier);

    /**
     * @param $identifier
     *
     * @return mixed
     */
    public function getPreviousStep($identifier);

    /**
     * @param $identifier
     *
     * @return bool
     */
    public function hasPreviousStep($identifier);

    /**
     * @param $identifier
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
     * @param $identifier
     *
     * @return mixed
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

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

interface CheckoutStepInterface
{
    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Determines if this step should be forward if valid.
     *
     * @param OrderInterface $cart
     *
     * @return bool
     */
    public function doAutoForward(OrderInterface $cart): bool;

    /**
     * Commit Step from Request (validate form or whatever).
     *
     * @param OrderInterface $cart
     * @param Request       $request
     *
     * @return bool
     *
     * @throws CheckoutException
     */
    public function commitStep(OrderInterface $cart, Request $request): bool;

    /**
     * Prepare Checkout Step.
     *
     * @param OrderInterface $cart
     * @param Request       $request
     *
     * @return array $params for the view
     */
    public function prepareStep(OrderInterface $cart, Request $request): array;
}

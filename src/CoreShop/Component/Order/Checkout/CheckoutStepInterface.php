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

interface CheckoutStepInterface
{
    public function getIdentifier(): string;

    /**
     * Determines if this step should be forward if valid.
     */
    public function doAutoForward(OrderInterface $cart): bool;

    /**
     * Commit Step from Request (validate form or whatever).
     *
     *
     *
     * @throws CheckoutException
     */
    public function commitStep(OrderInterface $cart, Request $request): bool;

    /**
     * Prepare Checkout Step.
     *
     *
     * @return array $params for the view
     */
    public function prepareStep(OrderInterface $cart, Request $request): array;
}

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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Model;

use Carbon\Carbon;

/**
 * Implemented by carts that remember when their customer last selected/activated
 * them. The timestamp lets the customer-and-store based context restore the
 * last-opened cart across sessions and devices, instead of merely the most
 * recently created one.
 */
interface ActivatableCartInterface
{
    public function getLastActivatedAt(): ?Carbon;

    public function setLastActivatedAt(?Carbon $lastActivatedAt): static;
}

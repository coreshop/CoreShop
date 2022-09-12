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

namespace CoreShop\Bundle\OrderBundle;

final class Events
{
    /**
     * Fired before a Sale (Cart, Order, Quote) is passed to the client
     */
    public const SALE_DETAIL_PREPARE = 'coreshop.sale.detail.prepare';

    /**
     * Fired when an Admin creates a new Customer via a CoreShop UI
     */
    public const ADMIN_CUSTOMER_CREATION = 'coreshop.customer.admin_creation';

    /**
     * Fired when an Admin creates a new Address via a CoreShop UI
     */
    public const ADMIN_ADDRESS_CREATION = 'coreshop.address.admin_creation';
}

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

namespace CoreShop\Bundle\OrderBundle;

final class Events
{
    /**
     * Fired before a Sale (Cart, Order, Quote) is passed to the client.
     */
    public const SALE_DETAIL_PREPARE = 'coreshop.sale.detail.prepare';

    /**
     * Fired when an Admin creates a new Customer via a CoreShop UI.
     */
    public const ADMIN_CUSTOMER_CREATION = 'coreshop.customer.admin_creation';

    /**
     * Fired when an Admin creates a new Address via a CoreShop UI.
     */
    public const ADMIN_ADDRESS_CREATION = 'coreshop.address.admin_creation';
}

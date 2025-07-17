<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\OrderBundle;

final class Events
{
    /**
     * Fired before a Sale (Cart, Order, Quote) is passed to the client
     */
    public const string SALE_DETAIL_PREPARE = 'coreshop.sale.detail.prepare';

    /**
     * Fired when an Admin creates a new Customer via a CoreShop UI
     */
    public const string ADMIN_CUSTOMER_CREATION = 'coreshop.customer.admin_creation';

    /**
     * Fired when an Admin creates a new Address via a CoreShop UI
     */
    public const string ADMIN_ADDRESS_CREATION = 'coreshop.address.admin_creation';

    /**
     * Fired when an Admin updates an Order in the Backend for preview
     */
    public const string ORDER_BACKEND_UPDATE_PREVIEW = 'coreshop.order.backend_update_preview';

    /**
     * Fired when an Admin updates an Order in the Backend for saving
     */
    public const string ORDER_BACKEND_UPDATE_SAVE = 'coreshop.order.backend_update_save';
}

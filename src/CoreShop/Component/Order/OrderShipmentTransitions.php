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

namespace CoreShop\Component\Order;

final class OrderShipmentTransitions
{
    public const string IDENTIFIER = 'coreshop_order_shipment';

    public const string TRANSITION_REQUEST_SHIPMENT = 'request_shipment';

    public const string TRANSITION_PARTIALLY_SHIP = 'partially_ship';

    public const string TRANSITION_CANCEL = 'cancel';

    public const string TRANSITION_SHIP = 'ship';
}

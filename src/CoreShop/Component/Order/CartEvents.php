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

namespace CoreShop\Component\Order;

class CartEvents
{
    public const PRE_REMOVE_ITEM = 'coreshop.cart.pre_remove_item';

    public const POST_REMOVE_ITEM = 'coreshop.cart.post_remove_item';

    public const PRE_ADD_ITEM = 'coreshop.cart.pre_add_item';

    public const POST_ADD_ITEM = 'coreshop.cart.post_add_item';
}

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

class CartEvents
{
    public const string PRE_REMOVE_ITEM = 'coreshop.cart.pre_remove_item';

    public const string POST_REMOVE_ITEM = 'coreshop.cart.post_remove_item';

    public const string PRE_ADD_ITEM = 'coreshop.cart.pre_add_item';

    public const string POST_ADD_ITEM = 'coreshop.cart.post_add_item';

    public const string PRE_UPDATE_ITEM = 'coreshop.cart.pre_update_item';

    public const string POST_UPDATE_ITEM = 'coreshop.cart.post_update_item';
}

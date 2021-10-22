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

namespace CoreShop\Component\Order;

class CartEvents
{
    public const PRE_REMOVE_ITEM = 'coreshop.cart.pre_remove_item';

    public const POST_REMOVE_ITEM = 'coreshop.cart.post_remove_item';

    public const PRE_ADD_ITEM = 'coreshop.cart.pre_add_item';

    public const POST_ADD_ITEM = 'coreshop.cart.post_add_item';
}

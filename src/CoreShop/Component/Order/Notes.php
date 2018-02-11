<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order;

final class Notes
{
    /**
     * Note Identifier for Payment
     */
    const NOTE_PAYMENT = 'Payment';

    /**
     * Note Identifier for Update Order
     */
    const NOTE_UPDATE_ORDER = 'Update Order';

    /**
     * Note Identifier for Update Order Item
     */
    const NOTE_UPDATE_ORDER_ITEM = 'Update Order Item';

    /**
     * Note Identifier for emails
     */
    const NOTE_EMAIL = 'Email';

    /**
     * Note Identifier for order comments
     */
    const NOTE_ORDER_COMMENT = 'OrderComment';

}
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

final class OrderPaymentStates
{
    public const STATE_NEW = 'new';

    public const STATE_PARTIALLY_AUTHORIZED = 'partially_authorized';

    public const STATE_AUTHORIZED = 'authorized';

    public const STATE_AWAITING_PAYMENT = 'awaiting_payment';

    public const STATE_PARTIALLY_PAID = 'partially_paid';

    public const STATE_CANCELLED = 'cancelled';

    public const STATE_PAID = 'paid';

    public const STATE_PARTIALLY_REFUNDED = 'partially_refunded';

    public const STATE_REFUNDED = 'refunded';
}

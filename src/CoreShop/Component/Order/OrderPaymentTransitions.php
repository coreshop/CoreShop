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

final class OrderPaymentTransitions
{
    public const IDENTIFIER = 'coreshop_order_payment';

    public const TRANSITION_REQUEST_PAYMENT = 'request_payment';

    public const TRANSITION_PARTIALLY_AUTHORIZE = 'partially_authorize';

    public const TRANSITION_AUTHORIZE = 'authorize';

    public const TRANSITION_PARTIALLY_PAY = 'partially_pay';

    public const TRANSITION_CANCEL = 'cancel';

    public const TRANSITION_PAY = 'pay';

    public const TRANSITION_PARTIALLY_REFUND = 'partially_refund';

    public const TRANSITION_REFUND = 'refund';
}

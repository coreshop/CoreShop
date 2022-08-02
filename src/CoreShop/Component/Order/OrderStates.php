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

final class OrderStates
{
    public const STATE_INITIALIZED = 'initialized';

    public const STATE_NEW = 'new';

    public const STATE_CONFIRMED = 'confirmed';

    public const STATE_CANCELLED = 'cancelled';

    public const STATE_COMPLETE = 'complete';
}

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

final class OrderTransitions
{
    public const IDENTIFIER = 'coreshop_order';

    public const TRANSITION_CREATE = 'create';

    public const TRANSITION_CONFIRM = 'confirm';

    public const TRANSITION_CANCEL = 'cancel';

    public const TRANSITION_COMPLETE = 'complete';
}

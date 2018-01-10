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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\StateMachine;

final class PaymentTransitions
{
    const IDENTIFIER = 'coreshop_payment';

    const TRANSITION_CREATE = 'create';
    const TRANSITION_PROCESS = 'process';
    const TRANSITION_COMPLETE = 'complete';
    const TRANSITION_FAIL = 'fail';
    const TRANSITION_CANCEL = 'cancel';
    const TRANSITION_REFUND = 'refund';
    const TRANSITION_VOID = 'void';
}

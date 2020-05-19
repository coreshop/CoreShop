<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order;

class CheckoutEvents
{
    public const CHECKOUT_STEP_PRE = 'coreshop.checkout.step.pre';
    public const CHECKOUT_STEP_POST = 'coreshop.checkout.step.post';

    public const CHECKOUT_DO_PRE = 'coreshop.checkout.do.pre';
    public const CHECKOUT_DO_POST = 'coreshop.checkout.do.post';
}

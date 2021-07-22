<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Cart\Rule\Applier;

if (interface_exists(CartRuleApplierInterface::class)) {
    @trigger_error('Interface CoreShop\Component\Core\Cart\Rule\Applier\DiscountApplierInterface is deprecated since version 2.1.0 and will be removed in 3.0.0. Use CoreShop\Component\Core\Cart\Rule\Applier\CartRuleApplierInterface class instead.', E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Interface CoreShop\Component\Core\Cart\Rule\Applier\DiscountApplierInterface is deprecated since version 2.1.0 and will be removed in 3.0.0. Use CoreShop\Component\Core\Cart\Rule\Applier\CartRuleApplierInterface class instead.
     */
    interface DiscountApplierInterface
    {
    }
}

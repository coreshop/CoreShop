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

namespace CoreShop\Component\Core\Cart\Rule\Applier;

if (class_exists(CartRuleApplier::class)) {
    @trigger_error('Class CoreShop\Component\Core\Cart\Rule\Applier\DiscountApplier is deprecated since version 2.1.0 and will be removed in 3.0.0. Use CoreShop\Component\Core\Cart\Rule\Applier\CartRuleApplier class instead.', E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Class CoreShop\Component\Core\Cart\Rule\Applier\DiscountApplier is deprecated since version 2.1.0 and will be removed in 3.0.0. Use CoreShop\Component\Core\Cart\Rule\Applier\CartRuleApplier class instead.
     */
    class DiscountApplier
    {
    }
}

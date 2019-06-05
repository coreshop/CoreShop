<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Payment\Resolver;

if (class_exists(\CoreShop\Bundle\PayumPaymentBundle\Resolver\EventBasedPaymentProviderResolver::class)) {
    @trigger_error('Class CoreShop\Bundle\CoreBundle\Payment\Resolver\EventBasedPaymentProviderResolver is deprecated since version 2.1.0 and will be removed in 3.0.0. Use CoreShop\Bundle\PayumPaymentBundle\Resolver\EventBasedPaymentProviderResolver class instead.', E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Class CoreShop\Bundle\CoreBundle\Payment\Resolver\EventBasedPaymentProviderResolver is deprecated since version 2.1.0 and will be removed in 3.0.0. Use CoreShop\Bundle\PayumPaymentBundle\Resolver\EventBasedPaymentProviderResolver class instead.
     */
    class EventBasedPaymentProviderResolver
    {
    }
}

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

namespace CoreShop\Component\Core\Shipping\Calculator;

use CoreShop\Component\Shipping\Calculator\TaxedShippingCalculatorInterface as NewTaxedShippingCalculatorInterface;

if (interface_exists(NewTaxedShippingCalculatorInterface::class)) {
    @trigger_error('Interface CoreShop\Component\Core\Shipping\Calculator\TaxedShippingCalculatorInterface is deprecated since version 2.2.0 and will be removed in 3.0.0. Use CoreShop\Component\Shipping\Calculator\TaxedShippingCalculatorInterface class instead.', E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Interface Interface CoreShop\Component\Core\Shipping\Calculator\TaxedShippingCalculatorInterface is deprecated since version 2.2.0 and will be removed in 3.0.0. Use CoreShop\Component\Shipping\Calculator\TaxedShippingCalculatorInterface class instead.
     */
    interface TaxedShippingCalculatorInterface
    {
    }
}

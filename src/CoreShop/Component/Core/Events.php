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

namespace CoreShop\Component\Core;

final class Events
{
    const SUPPORTS_PAYMENT_PROVIDER = 'coreshop.payment_provider.supports';

    const PRODUCT_STORE_VALUES_UMMARSHAL = 'coreshop.product_store_values.unmarshal';

    const PRODUCT_STORE_VALUES_UNIT_DEFINITION_PRICE_UMMARSHAL = 'coreshop.product_store_values_unit_definition_price.unmarshal';
}

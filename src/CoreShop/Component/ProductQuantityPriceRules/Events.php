<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\ProductQuantityPriceRules;

final class Events
{
    /**
     * Fired before quantity price rule data passes form process
     */
    public const RULES_DATA_FROM_EDITMODE_VALIDATION = 'coreshop.product_quantity_price_rules.data_validation';
}

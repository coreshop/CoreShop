<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class ProductSpecificPriceRuleActionPass extends RegisterRegistryTypePass
{
    public const PRODUCT_SPECIFIC_PRICE_RULE_ACTION_TAG = 'coreshop.product_specific_price_rule.action';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.product_specific_price_rule.actions',
            'coreshop.form_registry.product_specific_price_rule.actions',
            'coreshop.product_specific_price_rule.actions',
            self::PRODUCT_SPECIFIC_PRICE_RULE_ACTION_TAG,
        );
    }
}

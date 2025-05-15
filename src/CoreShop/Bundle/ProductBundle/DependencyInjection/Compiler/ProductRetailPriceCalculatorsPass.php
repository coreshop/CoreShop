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

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class ProductRetailPriceCalculatorsPass extends RegisterSimpleRegistryTypePass
{
    public const PRODUCT_RETAIL_PRICE_CALCULATOR_TAG = 'coreshop.product.retail_price_calculator';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.product.retail_price_calculators',
            'coreshop.product.retail_price_calculators',
            self::PRODUCT_RETAIL_PRICE_CALCULATOR_TAG,
        );
    }
}

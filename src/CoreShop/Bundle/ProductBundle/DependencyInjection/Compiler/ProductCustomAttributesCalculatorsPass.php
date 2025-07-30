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

namespace CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class ProductCustomAttributesCalculatorsPass extends RegisterSimpleRegistryTypePass
{
    public const string PRODUCT_CUSTOM_ATTRIBUTES_CALCULATOR_TAG = 'coreshop.product.custom_attributes_calculator';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.product.custom_attributes_calculators',
            'coreshop.product.custom_attributes_calculators',
            self::PRODUCT_CUSTOM_ATTRIBUTES_CALCULATOR_TAG,
        );
    }
}

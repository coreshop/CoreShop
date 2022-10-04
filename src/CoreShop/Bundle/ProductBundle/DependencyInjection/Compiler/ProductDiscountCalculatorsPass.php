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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class ProductDiscountCalculatorsPass extends RegisterSimpleRegistryTypePass
{
    public const PRODUCT_DISCOUNT_CALCULATOR_TAG = 'coreshop.product.discount_calculator';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.product.discount_calculators',
            'coreshop.product.discount_calculators',
            self::PRODUCT_DISCOUNT_CALCULATOR_TAG,
        );
    }
}

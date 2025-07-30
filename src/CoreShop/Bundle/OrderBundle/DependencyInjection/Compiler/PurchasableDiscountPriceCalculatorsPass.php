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

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class PurchasableDiscountPriceCalculatorsPass extends RegisterSimpleRegistryTypePass
{
    public const string PURCHASABLE_DISCOUNT_PRICE_CALCULATOR_TAG = 'coreshop.order.purchasable.discount_price_calculator';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.order.purchasable.discount_price_calculators',
            'coreshop.order.purchasable.discount_price_calculator',
            self::PURCHASABLE_DISCOUNT_PRICE_CALCULATOR_TAG,
        );
    }
}

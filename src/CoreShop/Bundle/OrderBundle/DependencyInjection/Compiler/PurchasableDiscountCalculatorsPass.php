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

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class PurchasableDiscountCalculatorsPass extends RegisterSimpleRegistryTypePass
{
    public const PURCHASABLE_DISCOUNT_CALCULATOR_TAG = 'coreshop.order.purchasable.discount_calculator';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.order.purchasable.discount_calculators',
            'coreshop.order.purchasable.discount_calculators',
            self::PURCHASABLE_DISCOUNT_CALCULATOR_TAG,
        );
    }
}

<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class PurchasablePriceCalculatorsPass extends RegisterSimpleRegistryTypePass
{
    public const PURCHASABLE_PRICE_CALCULATOR_TAG = 'coreshop.order.purchasable.price_calculator';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.order.purchasable.price_calculators',
            'coreshop.order.purchasable.price_calculators',
            self::PURCHASABLE_PRICE_CALCULATOR_TAG
        );
    }
}

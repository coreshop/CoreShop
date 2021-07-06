<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class ProductQuantityPriceRulesCalculatorPass extends RegisterSimpleRegistryTypePass
{
    public const PRODUCT_QUANTITY_PRICE_RULE_CALCULATOR_TAG = 'coreshop.product_quantity_price_rules.calculator';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.product_quantity_price_rules.calculators',
            'coreshop.product_quantity_price_rules.calculators',
            self::PRODUCT_QUANTITY_PRICE_RULE_CALCULATOR_TAG
        );
    }
}

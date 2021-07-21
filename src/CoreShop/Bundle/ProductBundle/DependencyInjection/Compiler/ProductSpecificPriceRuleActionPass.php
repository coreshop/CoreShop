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

declare(strict_types=1);

namespace CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class ProductSpecificPriceRuleActionPass extends RegisterRegistryTypePass
{
    public const PRODUCT_SPECIFIC_PRICE_RULE_ACTION_TAG = 'coreshop.product_specific_price_rule.action';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.product_specific_price_rule.actions',
            'coreshop.form_registry.product_specific_price_rule.actions',
            'coreshop.product_specific_price_rule.actions',
            self::PRODUCT_SPECIFIC_PRICE_RULE_ACTION_TAG
        );
    }
}

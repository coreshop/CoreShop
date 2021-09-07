<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class ProductDiscountCalculatorsPass extends RegisterSimpleRegistryTypePass
{
    public const PRODUCT_DISCOUNT_CALCULATOR_TAG = 'coreshop.product.discount_calculator';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.product.discount_calculators',
            'coreshop.product.discount_calculators',
            self::PRODUCT_DISCOUNT_CALCULATOR_TAG
        );
    }
}

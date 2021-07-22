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

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class PurchasableDiscountCalculatorsPass extends RegisterSimpleRegistryTypePass
{
    public const PURCHASABLE_DISCOUNT_CALCULATOR_TAG = 'coreshop.order.purchasable.discount_calculator';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.order.purchasable.discount_calculators',
            'coreshop.order.purchasable.discount_calculators',
            self::PURCHASABLE_DISCOUNT_CALCULATOR_TAG
        );
    }
}

<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CurrencyBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

final class CompositeCurrencyContextPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'coreshop.context.currency',
            'coreshop.context.currency.composite',
            'coreshop.context.currency',
            'addContext'
        );
    }
}

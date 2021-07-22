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

use CoreShop\Component\Registry\PrioritizedCompositeServicePass;

final class RegisterCartContextsPass extends PrioritizedCompositeServicePass
{
    public const CART_CONTEXT_TAG = 'coreshop.context.cart';

    public function __construct()
    {
        parent::__construct(
            'coreshop.context.cart',
            'coreshop.context.cart.composite',
            self::CART_CONTEXT_TAG,
            'addContext'
        );
    }
}

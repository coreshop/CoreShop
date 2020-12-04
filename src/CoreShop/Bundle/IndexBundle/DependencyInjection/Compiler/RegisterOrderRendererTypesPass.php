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

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

class RegisterOrderRendererTypesPass extends RegisterSimpleRegistryTypePass
{
    public const INDEX_ORDER_RENDERER_TAG = 'coreshop.index.order.renderer';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.index.order.renderers',
            'coreshop.index.order.renderers',
            self::INDEX_ORDER_RENDERER_TAG
        );
    }
}

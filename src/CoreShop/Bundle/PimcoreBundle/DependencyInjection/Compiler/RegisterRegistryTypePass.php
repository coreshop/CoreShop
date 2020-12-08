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

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass as NewRegisterRegistryTypePass;

if (class_exists(NewRegisterRegistryTypePass::class)) {
    @trigger_error('Class CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterRegistryTypePass is deprecated since version 2.2.6 and will be removed in 3.0.0. Use CoreShop\Component\Registry\RegisterRegistryTypePass class instead.', E_USER_DEPRECATED);
} else {
    /**
     * @deprecated Class CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterRegistryTypePass is deprecated since version 2.2.6 and will be removed in 3.0.0. Use CoreShop\Component\Registry\RegisterRegistryTypePass class instead.
     */
    class RegisterRegistryTypePass extends NewRegisterRegistryTypePass
    {
    }
}

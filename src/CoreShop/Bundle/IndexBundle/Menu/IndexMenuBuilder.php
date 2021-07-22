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

namespace CoreShop\Bundle\IndexBundle\Menu;

use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class IndexMenuBuilder implements MenuBuilderInterface
{
    public function buildMenu(ItemInterface $menuItem, FactoryInterface $factory, string $type)
    {
        $menuItem->setLabel('coreshop');
        $menuItem->setAttributes([
            'class' => 'coreshop_logo_menu',
            'content' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 76 87">
                    <defs>
                        <style>.cls-1{fill:#cd1017;fill-rule:evenodd;}</style>
                    </defs>
                    <path class="cls-1"
                          d="M48.57,50.58,38,56.69,14.31,43V29.82L38,16.15l31.87,18.4V25.1L38,6.7,6.13,25.1V47.73L38,66.13,56.75,55.3V45.86l-8.18-4.72v9.44ZM27.43,36.42,38,30.31,61.69,44V57.18L38,70.85,6.13,52.45V61.9L38,80.3,69.87,61.9V39.27L38,20.87,19.25,31.7v9.44l8.18,4.72V36.42Z"/>
                </svg>
            ',
        ]);

        $menuItem
            ->addChild('coreshop_indexes')
            ->setLabel('coreshop_indexes')
            ->setAttribute('permission', 'coreshop_permission_index')
            ->setAttribute('iconCls', 'coreshop_nav_icon_indexes')
            ->setAttribute('resource', 'coreshop.index')
            ->setAttribute('function', 'index');

        $menuItem
            ->addChild('coreshop_filters')
            ->setLabel('coreshop_filters')
            ->setAttribute('permission', 'coreshop_permission_filter')
            ->setAttribute('iconCls', 'coreshop_nav_icon_filters')
            ->setAttribute('resource', 'coreshop.index')
            ->setAttribute('function', 'filter');
    }
}

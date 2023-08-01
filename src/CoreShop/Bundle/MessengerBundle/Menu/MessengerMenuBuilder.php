<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\MessengerBundle\Menu;

use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MessengerMenuBuilder implements MenuBuilderInterface
{
    public function buildMenu(ItemInterface $menuItem, FactoryInterface $factory, string $type): void
    {
        $menuItem
            ->addChild('coreshop_messenger')
            ->setLabel('coreshop_messenger')
            ->setAttribute('permission', 'coreshop_permission_messenger')
            ->setAttribute('iconCls', 'coreshop_nav_icon_messenger')
            ->setAttribute('resource', 'coreshop.messenger')
            ->setAttribute('function', 'list')
        ;
    }
}

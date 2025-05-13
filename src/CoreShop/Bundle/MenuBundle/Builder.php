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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\MenuBundle;

use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class Builder
{
    public function __construct(
        protected FactoryInterface $factory,
        protected string $type,
        protected ServiceRegistryInterface $registry,
    ) {
    }

    public function createMenu(): ItemInterface
    {
        $menu = $this->factory->createItem($this->type);

        foreach ($this->registry->all() as $menuBuilder) {
            if (!$menuBuilder instanceof MenuBuilderInterface) {
                continue;
            }

            $menuBuilder->buildMenu($menu, $this->factory, $this->type);
        }

        return $menu;
    }
}

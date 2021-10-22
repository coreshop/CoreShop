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

namespace CoreShop\Bundle\MenuBundle;

use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class Builder
{
    public function __construct(protected FactoryInterface $factory, protected string $type, protected ServiceRegistryInterface $registry)
    {
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

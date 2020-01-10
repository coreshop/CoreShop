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

namespace CoreShop\Bundle\MenuBundle;

use CoreShop\Bundle\MenuBundle\Builder\MenuBuilderInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Knp\Menu\FactoryInterface;

class Builder
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var ServiceRegistryInterface
     */
    protected $registry;

    /**
     * @param FactoryInterface         $factory
     * @param string                   $type
     * @param ServiceRegistryInterface $registry
     */
    public function __construct(FactoryInterface $factory, string $type, ServiceRegistryInterface $registry)
    {
        $this->factory = $factory;
        $this->type = $type;
        $this->registry = $registry;
    }

    public function createMenu()
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

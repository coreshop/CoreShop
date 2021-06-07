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

declare(strict_types=1);

namespace CoreShop\Bundle\MenuBundle\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

interface MenuBuilderInterface
{
    /**
     * @param ItemInterface    $menuItem
     * @param FactoryInterface $factory
     * @param string           $type
     */
    public function buildMenu(ItemInterface $menuItem, FactoryInterface $factory, string $type): void;
}

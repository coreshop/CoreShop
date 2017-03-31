<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Address\Model\Country;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\Zone;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Core\Factory\Factory;
use CoreShop\Component\Core\Factory\ListingFactory;
use CoreShop\Component\Core\Repository\Repository;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('coreshop_core');

        return $treeBuilder;
    }
}

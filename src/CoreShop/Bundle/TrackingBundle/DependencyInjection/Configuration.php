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

namespace CoreShop\Bundle\TrackingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('core_shop_tracking');
        $rootNode = $treeBuilder->getRootNode();

        $this->buildTrackingNode($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function buildTrackingNode(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('trackers')
                    ->useAttributeAsKey('type')
                    ->normalizeKeys(false)
                    ->arrayPrototype()
                        ->children()
                            ->booleanNode('enabled')->defaultFalse()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}

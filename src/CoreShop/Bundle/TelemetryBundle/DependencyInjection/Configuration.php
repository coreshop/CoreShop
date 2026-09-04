<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\TelemetryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('core_shop_telemetry');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('enabled')
                    ->info('Daily anonymous ping to the CoreShop license portal. Set CORESHOP_TELEMETRY=false to opt out.')
                    ->defaultValue('%env(bool:CORESHOP_TELEMETRY)%')
                ->end()
                ->scalarNode('endpoint')->defaultValue('https://license.coreshop.com/v1/ping')->cannotBeEmpty()->end()
                ->floatNode('timeout')->defaultValue(4.0)->min(0.5)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

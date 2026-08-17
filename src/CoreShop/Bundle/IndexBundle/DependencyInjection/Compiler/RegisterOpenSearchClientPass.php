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

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterOpenSearchClientPass implements CompilerPassInterface
{
    /**
     * Wrapper clients implementing Pimcore\SearchClient\SearchClientInterface,
     * registered by pimcore/opensearch-client and pimcore/elasticsearch-client.
     * OpenSearch is scanned first so its clients keep their bare identifier on
     * a name collision (BC with configurations created before Elasticsearch support).
     */
    private const array CLIENT_SERVICE_PREFIXES = [
        'opensearch' => 'pimcore.openSearch.custom_client.',
        'elasticsearch' => 'pimcore.elasticsearch.custom_client.',
    ];

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->getDefinition('coreshop.registry.index.opensearch_client');
        $registered = [];

        foreach (self::CLIENT_SERVICE_PREFIXES as $engine => $prefix) {
            foreach ($container->getDefinitions() as $id => $definition) {
                if (!\str_starts_with($id, $prefix)) {
                    continue;
                }

                $identifier = \substr($id, \strlen($prefix));

                if (isset($registered[$identifier])) {
                    $identifier = $engine . '_' . $identifier;
                }

                $registered[$identifier] = true;
                $registry->addMethodCall('register', [$identifier, new Reference($id)]);
            }
        }
    }
}

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

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterOpenSearchClientPass implements CompilerPassInterface
{
    private const CLIENT_SERVICE_PREFIX = 'pimcore.open_search_client.';

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->getDefinition('coreshop.registry.index.opensearch_client');

        foreach ($container->getDefinitions() as $id => $definition) {
            if (\str_starts_with($id, self::CLIENT_SERVICE_PREFIX)) {
                $identifier = \str_replace(self::CLIENT_SERVICE_PREFIX, '', $id);
                $registry->addMethodCall('register', [$identifier, new Reference($id)]);
            }
        }
    }
}

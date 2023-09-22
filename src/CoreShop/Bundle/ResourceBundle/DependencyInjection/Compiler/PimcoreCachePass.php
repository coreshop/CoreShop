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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\Pimcore\CacheResourceMarshaller;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class PimcoreCachePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('pimcore.cache.adapter.pdo')->setArgument(4, []);
        $container->getDefinition('pimcore.cache.adapter.pdo')->setArgument(5, new Reference(CacheResourceMarshaller::class));

        $container->getDefinition('pimcore.cache.adapter.doctrine_dbal')->setArgument(4, []);
        $container->getDefinition('pimcore.cache.adapter.doctrine_dbal')->setArgument(5, new Reference(CacheResourceMarshaller::class));

        $container->getDefinition('pimcore.cache.adapter.redis_tag_aware')->setArgument(4, new Reference(CacheResourceMarshaller::class));
    }
}

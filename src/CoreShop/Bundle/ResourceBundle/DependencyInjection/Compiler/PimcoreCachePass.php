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

        $container->getDefinition('pimcore.cache.adapter.redis_tag_aware')->setArgument(4, CacheResourceMarshaller::class);
    }
}

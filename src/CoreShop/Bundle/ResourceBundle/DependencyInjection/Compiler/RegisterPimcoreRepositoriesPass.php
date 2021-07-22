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

use CoreShop\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterPimcoreRepositoriesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('pimcore.dao.object_manager')) {
            return;
        }

        $registry = $container->get(RegistryInterface::class);

        foreach ($container->findTaggedServiceIds('coreshop.pimcore.repository') as $id => $attributes) {
            foreach ($attributes as $tag) {
                if (!isset($tag['alias'])) {
                    throw new \InvalidArgumentException('Tagged Repository `'.$id.'` needs to have `type` and `priority` attributes.');
                }

                $metadata = $registry->get($tag['alias']);

                $container->findDefinition('pimcore.dao.object_manager')->addMethodCall(
                    'registerRepository',
                    [
                        $metadata->getClass('model'),
                        new Reference($id),
                    ]
                );
            }
        }
    }
}

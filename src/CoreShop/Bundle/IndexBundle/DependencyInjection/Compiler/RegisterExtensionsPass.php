<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterExtensionsPass implements CompilerPassInterface
{
    public const INDEX_EXTENSION_TAG = 'coreshop.index.extension';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('coreshop.registry.index.extensions')) {
            return;
        }

        $registry = $container->getDefinition('coreshop.registry.index.extensions');

        foreach ($container->findTaggedServiceIds(self::INDEX_EXTENSION_TAG) as $id => $attributes) {
            $registry->addMethodCall('register', [$id, new Reference($id)]);
        }
    }
}

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

declare(strict_types=1);

namespace CoreShop\Component\Registry;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class RegisterRegistryTypePass implements CompilerPassInterface
{
    protected string $registry;
    protected string $formRegistry;
    protected string $parameter;
    protected string $tag;

    public function __construct(string $registry, string $formRegistry, string $parameter, string $tag)
    {
        $this->registry = $registry;
        $this->formRegistry = $formRegistry;
        $this->parameter = $parameter;
        $this->tag = $tag;
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has($this->registry) || !$container->has($this->formRegistry)) {
            return;
        }

        $registry = $container->getDefinition($this->registry);
        $formRegistry = $container->getDefinition($this->formRegistry);

        $map = [];
        foreach ($container->findTaggedServiceIds($this->tag) as $id => $attributes) {
            foreach ($attributes as $tag) {
                $definition = $container->findDefinition($id);

                if (!isset($tag['type'])) {
                    $tag['type'] = Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1));
                }

                $map[$tag['type']] = $tag['type'];

                $registry->addMethodCall('register', [$tag['type'], new Reference($id)]);

                if (isset($tag['form-type'])) {
                    $formRegistry
                        ->addMethodCall('add', [$tag['type'], 'default', $tag['form-type']]);
                }
            }
        }

        $container->setParameter($this->parameter, $map);
    }
}

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

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class RegisterImplementationLoaderPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    protected $implementationLoader;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @param string $implementationLoader
     * @param string $tag
     */
    public function __construct(string $implementationLoader, string $tag)
    {
        $this->implementationLoader = $implementationLoader;
        $this->tag = $tag;
    }

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->implementationLoader)) {
            return;
        }

        $registry = $container->getDefinition($this->implementationLoader);

        foreach ($container->findTaggedServiceIds($this->tag) as $id => $attributes) {
            $registry->addMethodCall('addLoader', [new Reference($id)]);
        }
    }
}

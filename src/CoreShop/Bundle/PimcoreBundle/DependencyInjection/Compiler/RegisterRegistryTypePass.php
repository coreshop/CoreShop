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

namespace CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class RegisterRegistryTypePass implements CompilerPassInterface
{
    /**
     * @var string
     */
    protected $registry;

    /**
     * @var string
     */
    protected $formRegistry;

    /**
     * @var string
     */
    protected $parameter;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @param string $registry
     * @param string $formRegistry
     * @param string $parameter
     * @param string $tag
     */
    public function __construct($registry, $formRegistry, $parameter, $tag)
    {
        $this->registry = $registry;
        $this->formRegistry = $formRegistry;
        $this->parameter = $parameter;
        $this->tag = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->registry) || !$container->has($this->formRegistry)) {
            return;
        }

        $registry = $container->getDefinition($this->registry);
        $formRegistry = $container->getDefinition($this->formRegistry);

        $map = [];
        foreach ($container->findTaggedServiceIds($this->tag) as $id => $attributes) {
            if (!isset($attributes[0]['type'], $attributes[0]['form-type'])) {
                throw new \InvalidArgumentException('Tagged Service `' . $id . '` needs to have `type` and `form-type` attributes.');
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];

            $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
            $formRegistry->addMethodCall('add', [$attributes[0]['type'], 'default', $attributes[0]['form-type']]);
        }

        $container->setParameter($this->parameter, $map);
    }
}

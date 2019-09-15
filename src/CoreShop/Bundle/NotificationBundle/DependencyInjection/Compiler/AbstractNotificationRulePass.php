<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\NotificationBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterRegistryTypePass;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistry;
use CoreShop\Component\Registry\ServiceRegistry;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractNotificationRulePass extends RegisterRegistryTypePass
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $registry
     * @param string $formRegistry
     * @param string $parameter
     * @param string $tag
     * @param string $type
     */
    public function __construct($registry, $formRegistry, $parameter, $tag, $type)
    {
        parent::__construct($registry, $formRegistry, $parameter, $tag);

        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->registry) || !$container->has($this->formRegistry)) {
            return;
        }

        $registries = [];
        $formRegistries = [];
        $types = [];
        $registeredTypes = [];

        $registry = $container->getDefinition($this->registry);
        $formRegistry = $container->getDefinition($this->formRegistry);

        $map = [];
        foreach ($container->findTaggedServiceIds($this->tag) as $id => $attributes) {
            foreach ($attributes as $tag) {
                $definition = $container->findDefinition($id);

                if (!isset($attributes[0]['type'])) {
                    $attributes[0]['type'] = Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1));
                }

                if (!isset($tag['notification-type'])) {
                    throw new \InvalidArgumentException('Tagged Condition `' . $id . '` needs to have `notification-type`` attribute.');
                }

                $type = $tag['notification-type'];

                if (!array_key_exists($type, $registries)) {
                    $registries[$type] = new Definition(
                        ServiceRegistry::class,
                        [ConditionCheckerInterface::class, 'notification-rule-' . $this->type . '-' . $type]
                    );

                    $formRegistries[$type] = new Definition(
                        FormTypeRegistry::class
                    );

                    $types[] = $type;

                    $container->setDefinition($this->registry . '.' . $type, $registries[$type]);
                    $container->setDefinition($this->formRegistry . '.' . $type, $formRegistries[$type]);
                }

                $map[$tag['notification-type']][$tag['type']] = $tag['type'];

                $fqtn = sprintf('%s.%s', $type, $tag['type']);

                $registries[$type]->addMethodCall('register', [$tag['type'], new Reference($id)]);
                $registry->addMethodCall('register', [$fqtn, new Reference($id)]);

                if (isset($attributes[0]['form-type'])) {
                    $formRegistries[$type]->addMethodCall('add', [$tag['type'], 'default', $tag['form-type']]);
                    $formRegistry->addMethodCall('add', [$fqtn, 'default', $tag['form-type']]);
                }

                $registeredTypes[$fqtn] = $fqtn;
            }
        }

        foreach ($map as $type => $realMap) {
            $container->setParameter($this->parameter . '.' . $type, $realMap);
        }

        $container->setParameter($this->parameter . '.types', $types);
        $container->setParameter($this->parameter, $registeredTypes);
    }
}

<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\NotificationBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistry;
use CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler\RegisterActionConditionPass;
use CoreShop\Component\Registry\ServiceRegistry;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractNotificationRulePass extends RegisterActionConditionPass
{
    /**
     * @return string
     */
    abstract protected function getType();

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->getRegistryIdentifier()) || !$container->has($this->getFormRegistryIdentifier())) {
            return;
        }

        $registries = [];
        $formRegistries = [];
        $types = [];
        $registeredTypes = [];

        $registry = $container->getDefinition($this->getRegistryIdentifier());
        $formRegistry = $container->getDefinition($this->getFormRegistryIdentifier());

        $map = [];
        foreach ($container->findTaggedServiceIds($this->getTagIdentifier()) as $id => $attributes) {
            foreach ($attributes as $tag) {
                if (!isset($tag['type'], $tag['form-type'], $tag['notification-type'])) {
                    throw new \InvalidArgumentException('Tagged Condition `'.$id.'` needs to have `type`, `form-type` and `notification-type`` attributes.');
                }

                $type = $tag['notification-type'];

                if (!array_key_exists($type, $registries)) {
                    $registries[$type] = new Definition(
                        ServiceRegistry::class,
                        [ConditionCheckerInterface::class, 'notification-rule-'.$this->getType().'-'.$type]
                    );

                    $formRegistries[$type] = new Definition(
                        FormTypeRegistry::class
                    );

                    $types[] = $type;

                    $container->setDefinition($this->getRegistryIdentifier().'.'.$type, $registries[$type]);
                    $container->setDefinition($this->getFormRegistryIdentifier().'.'.$type, $formRegistries[$type]);
                }

                $map[$tag['notification-type']][$tag['type']] = $tag['type'];

                $fqtn = sprintf('%s.%s', $type, $tag['type']);

                $registries[$type]->addMethodCall('register', [$tag['type'], new Reference($id)]);
                $formRegistries[$type]->addMethodCall('add', [$tag['type'], 'default', $tag['form-type']]);

                $registry->addMethodCall('register', [$fqtn, new Reference($id)]);
                $formRegistry->addMethodCall('add', [$fqtn, 'default', $tag['form-type']]);

                $registeredTypes[$fqtn] = $fqtn;
            }
        }

        foreach ($map as $type => $realMap) {
            $container->setParameter($this->getIdentifier().'.'.$type, $realMap);
        }

        $container->setParameter($this->getIdentifier().'.types', $types);
        $container->setParameter($this->getIdentifier(), $registeredTypes);
    }
}

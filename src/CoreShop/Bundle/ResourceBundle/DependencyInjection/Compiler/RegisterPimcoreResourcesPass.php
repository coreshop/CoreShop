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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler;

use CoreShop\Component\Resource\Helper\Tool;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

final class RegisterPimcoreResourcesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $resources = $container->getParameter('coreshop.all.pimcore_classes');
            $registry = $container->findDefinition('coreshop.resource_registry');
        } catch (InvalidArgumentException $exception) {
            return;
        }

        $pimcoreClasses = [
        ];
        $applicationClasses = [];

        foreach ($resources as $alias => $configuration) {
            list($applicationName, $resourceName) = explode('.', $alias);

            if (!array_key_exists($applicationName, $applicationClasses)) {
                $applicationClasses[$applicationName] = [];
            }

            //Causes installation problems
            $this->validateCoreShopPimcoreModel($configuration['classes']['model'], $configuration['classes']['interface']);
            $registry->addMethodCall('addFromAliasAndConfiguration', [$alias, $configuration]);

            if (Tool::classExists($configuration['classes']['model'])) {
                $class = $configuration['classes']['model'];

                if (method_exists($class, 'classId')) {
                    $classId = $class::classId();

                    $container->setParameter(sprintf('%s.model.%s.pimcore_class_id', $applicationName, $resourceName), $classId);

                    $applicationClasses[$applicationName][$resourceName] = $classId;
                    $pimcoreClasses[sprintf('%s.%s', $applicationName, $resourceName)] = $classId;
                }
            }
        }

        foreach ($applicationClasses as $applicationName => $values) {
            $container->setParameter(sprintf('%s.pimcore_classes.ids', $applicationName), $values);
        }

        $container->setParameter('coreshop.all.pimcore_classes.ids', $pimcoreClasses);
    }

    /**
     * @param $class
     * @param $interface
     */
    private function validateCoreShopPimcoreModel($class, $interface)
    {
        //TODO: Needs to be solved different. Everytime you make a mistake on class-creation
        //this stops pimcore from being functional :/
        /*if (Tool::classExists($class)) {
            if (!in_array($interface, class_implements($class), true)) {
                throw new InvalidArgumentException(sprintf(
                    'Class "%s" must implement "%s" to be registered as a CoreShop Pimcore model.',
                    $class,
                    $interface
                ));
            }
        }*/
    }
}

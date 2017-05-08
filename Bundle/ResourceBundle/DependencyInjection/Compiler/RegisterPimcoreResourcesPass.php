<?php

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
            $resources = $container->getParameter('coreshop.pimcore');
            $registry = $container->findDefinition('coreshop.resource_registry');
        } catch (InvalidArgumentException $exception) {
            return;
        }

        foreach ($resources as $alias => $configuration) {
            list($applicationName, $resourceName) = explode('.', $alias);

            //Causes installation problems
            $this->validateCoreShopPimcoreModel($configuration['classes']['model'], $configuration['classes']['interface']);
            $registry->addMethodCall('addFromAliasAndConfiguration', [$alias, $configuration]);

            if (Tool::classExists($configuration['classes']['model'])) {
                $class = $configuration['classes']['model'];

                if (method_exists($class, 'classId')) {
                    $classId = $class::classId();

                    $container->setParameter(sprintf('%s.model.%s.pimcore_class_id', $applicationName, $resourceName), $classId);
                }
            }
        }
    }

    /**
     * @param $class
     * @param $interface
     */
    private function validateCoreShopPimcoreModel($class, $interface)
    {
        //TODO: Needs to be solved different. Everytime you make a mistake on class-creation
        //this stops pimcore from being functional :/

        if (Tool::classExists($class)) {
            if (!in_array($interface, class_implements($class), true)) {
                throw new InvalidArgumentException(sprintf(
                    'Class "%s" must implement "%s" to be registered as a CoreShop Pimcore model.',
                    $class,
                    $interface
                ));
            }
        }
    }
}

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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DataHub\Type\ResourceType;
use CoreShop\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use CoreShop\Component\Resource\Metadata\MetadataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class DoctrineORMDriver extends AbstractDoctrineDriver
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return CoreShopResourceBundle::DRIVER_DOCTRINE_ORM;
    }

    /**
     * {@inheritdoc}
     */
    protected function addDataHubServices(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $typeParameterName = sprintf('%s.data_hub.object_type.%s', $metadata->getApplicationName(), $metadata->getName());
        $typeClass = ResourceType::class;

        if ($container->hasParameter($typeParameterName)) {
            $typeClass = $container->getParameter($typeParameterName);
        }

        if ($metadata->hasClass('data_hub')) {
            $typeClass = $metadata->getClass('data_hub');
        }

        $definition = new Definition($typeClass);
        $definition->setPublic(true);
        $definition->setArguments([
            new Reference('doctrine.orm.entity_manager'),
            $this->getClassMetadataDefinition($metadata),
        ]);

        $container->setDefinition($metadata->getServiceId('data_hub.type'), $definition);
    }

    /**
     * {@inheritdoc}
     */
    protected function addRepository(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $repositoryClassParameterName = sprintf('%s.repository.%s.class', $metadata->getApplicationName(), $metadata->getName());
        $repositoryClass = EntityRepository::class;

        if ($container->hasParameter($repositoryClassParameterName)) {
            $repositoryClass = $container->getParameter($repositoryClassParameterName);
        }

        if ($metadata->hasClass('repository')) {
            $repositoryClass = $metadata->getClass('repository');
        }

        $definition = new Definition($repositoryClass);
        $definition->setPublic(true);
        $definition->setArguments([
            new Reference($metadata->getServiceId('manager')),
            $this->getClassMetadataDefinition($metadata),
        ]);

        $container->setDefinition($metadata->getServiceId('repository'), $definition);

        if (method_exists($container, 'registerAliasForArgument')) {
            foreach (class_implements($repositoryClass) as $typehintClass) {
                $container->registerAliasForArgument(
                    $metadata->getServiceId('repository'),
                    $typehintClass,
                    $metadata->getHumanizedName() . ' repository'
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function addManager(ContainerBuilder $container, MetadataInterface $metadata): void
    {
        parent::addManager($container, $metadata);

        if (method_exists($container, 'registerAliasForArgument')) {
            $container->registerAliasForArgument(
                $metadata->getServiceId('manager'),
                EntityManagerInterface::class,
                $metadata->getHumanizedName() . ' manager'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getManagerServiceId(MetadataInterface $metadata)
    {
        if ($objectManagerName = $this->getObjectManagerName($metadata)) {
            return sprintf('doctrine.orm.%s_entity_manager', $objectManagerName);
        }

        return 'doctrine.orm.entity_manager';
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataClassname()
    {
        return ClassMetadata::class;
    }
}

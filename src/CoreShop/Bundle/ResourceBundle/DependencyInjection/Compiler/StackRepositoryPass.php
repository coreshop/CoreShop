<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepository;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\StackRepositoryInterface;
use CoreShop\Component\Resource\Metadata\Metadata;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class StackRepositoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('coreshop.all.stack.pimcore_class_names') ||
            !$container->hasParameter('coreshop.all.stack')) {
            return;
        }

        /**
         * @var array $stackConfig
         */
        $stackConfig = $container->getParameter('coreshop.all.stack');

        /**
         * @var array $fqcns
         */
        $fqcns = $container->getParameter('coreshop.all.stack.fqcns');

        foreach ($fqcns as $alias => $classes) {
            [$applicationName, $name] = explode('.', $alias);

            $definition = new Definition(Metadata::class);
            $definition
                ->setFactory([Metadata::class, 'fromAliasAndConfiguration'])
                ->setArguments([$alias, ['driver' => CoreShopResourceBundle::DRIVER_PIMCORE]])
            ;

            $repositoryDefinition = new Definition(StackRepository::class);
            $repositoryDefinition->setArguments([
                $definition,
                new Reference('doctrine.dbal.default_connection'),
                $stackConfig[$alias],
                $classes,
            ]);

            $serviceId = sprintf('%s.repository.stack.%s', $applicationName, $name);
            $container->setDefinition($serviceId, $repositoryDefinition)
                ->setPublic(true)
            ;

            $container->registerAliasForArgument(
                $serviceId,
                StackRepository::class,
                $alias . ' stack repository',
            );
            $container->registerAliasForArgument(
                $serviceId,
                StackRepositoryInterface::class,
                $alias . ' stack repository',
            );
        }
    }
}

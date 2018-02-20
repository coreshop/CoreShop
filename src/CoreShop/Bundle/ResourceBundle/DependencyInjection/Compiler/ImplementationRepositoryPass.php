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

use CoreShop\Bundle\OrderBundle\Pimcore\Repository\PurchasableRepository;
use CoreShop\Bundle\ResourceBundle\Pimcore\Repository\ImplementationRepository;
use CoreShop\Component\Resource\Metadata\Metadata;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class ImplementationRepositoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('coreshop.all.implementations.pimcore_class_names') ||
            !$container->hasParameter('coreshop.all.implementations')) {
            return;
        }

        $implementations = $container->getParameter('coreshop.all.implementations');

        foreach ($container->getParameter('coreshop.all.implementations.pimcore_class_names') as $alias => $classes) {
            list ($applicationName, $implementation) = explode('.', $alias);

            $definition = new Definition(Metadata::class);
            $definition
                ->setFactory([Metadata::class, 'fromAliasAndConfiguration'])
                ->setArguments([$alias, []]);

            $repositoryDefinition = new Definition(ImplementationRepository::class);
            $repositoryDefinition->setArguments([
                $definition,
                $implementations[$alias],
                $classes
            ]);

            $container->setDefinition(sprintf('%s.repository.implementation.%s', $applicationName, $implementation), $repositoryDefinition);
        }
    }
}

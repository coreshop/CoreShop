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

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\OrderBundle\Pimcore\Repository\PurchasableRepository;
use CoreShop\Component\Resource\Metadata\Metadata;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class PurchasableRepositoryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = new Definition(Metadata::class);
        $definition
            ->setFactory([Metadata::class, 'fromAliasAndConfiguration'])
            ->setArguments(['coreshop.purchasable', []]);

        $repositoryDefinition = new Definition(PurchasableRepository::class);
        $repositoryDefinition->setArguments([
            $definition,
            $container->getParameter('coreshop.implementations.purchasable.pimcore_class_names')
        ]);

        $container->setDefinition('coreshop.repository.purchasable', $repositoryDefinition);
    }
}

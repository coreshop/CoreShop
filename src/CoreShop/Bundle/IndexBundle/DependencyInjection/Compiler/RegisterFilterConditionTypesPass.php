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

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterFilterConditionTypesPass implements CompilerPassInterface
{
    public const INDEX_FILTER_CONDITION_TAG = 'coreshop.filter.condition_type';

    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds(self::INDEX_FILTER_CONDITION_TAG) as $id => $attributes) {
            $definition = $container->findDefinition($id);

            $definition->addTag('coreshop.filter.user_condition_type', $attributes[0]);
            $definition->addTag('coreshop.filter.pre_condition_type', $attributes[0]);
        }
    }
}

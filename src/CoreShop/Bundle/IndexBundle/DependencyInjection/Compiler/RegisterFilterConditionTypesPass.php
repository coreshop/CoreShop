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

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterFilterConditionTypesPass implements CompilerPassInterface
{
    public const INDEX_FILTER_CONDITION_TAG = 'coreshop.filter.condition_type';

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds(self::INDEX_FILTER_CONDITION_TAG) as $id => $attributes) {
            $definition = $container->findDefinition($id);

            foreach ($attributes as $tag) {
                if (empty($tag)) {
                    continue;
                }

                $definition->addTag('coreshop.filter.user_condition_type', $tag);
                $definition->addTag('coreshop.filter.pre_condition_type', $tag);
            }
        }
    }
}

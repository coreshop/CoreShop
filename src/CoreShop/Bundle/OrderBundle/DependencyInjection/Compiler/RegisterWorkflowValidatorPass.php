<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterWorkflowValidatorPass implements CompilerPassInterface
{
    public const string WORKFLOW_VALIDATOR_TAG = 'coreshop.workflow.validator';

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds(self::WORKFLOW_VALIDATOR_TAG) as $id => $attributes) {
            $definition = $container->findDefinition($id);

            foreach ($attributes as $tag) {
                if (!isset($tag['type'])) {
                    $tag['type'] = Container::underscore(substr((string) strrchr($definition->getClass(), '\\'), 1));
                }

                if (!isset($tag['manager'])) {
                    throw new \InvalidArgumentException('Tagged Condition `' . $id . '` needs to have `manager` attribute.');
                }

                $manager = $container->getDefinition($tag['manager']);

                $priority = isset($tag['priority']) ? (int) $tag['priority'] : 0;

                $manager->addMethodCall('addValidator', [new Reference($id), $tag['type'], $priority]);
            }
        }
    }
}

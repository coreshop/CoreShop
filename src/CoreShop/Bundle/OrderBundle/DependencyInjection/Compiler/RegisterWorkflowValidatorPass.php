<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterWorkflowValidatorPass implements CompilerPassInterface
{
    public const WORKFLOW_VALIDATOR_TAG = 'coreshop.workflow.validator';

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds(self::WORKFLOW_VALIDATOR_TAG) as $id => $attributes) {
            $definition = $container->findDefinition($id);

            foreach ($attributes as $tag) {
                if (!isset($tag['type'])) {
                    $tag['type'] = Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1));
                }

                if (!isset($tag['manager'])) {
                    throw new \InvalidArgumentException('Tagged Condition `'.$id.'` needs to have `manager` attribute.');
                }

                $manager = $container->getDefinition($tag['manager']);

                if (!$manager) {
                    throw new \InvalidArgumentException(
                        sprintf('Workflow Manager with identifier %s not found', $tag['manager'])
                    );
                }

                $priority = isset($tag['priority']) ? (int)$tag['priority'] : 0;

                $manager->addMethodCall('addValidator', [new Reference($id), $tag['type'], $priority]);
            }
        }
    }
}

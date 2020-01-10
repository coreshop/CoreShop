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

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterWorkflowValidatorPass implements CompilerPassInterface
{
    public const WORKFLOW_VALIDATOR_TAG = 'coreshop.workflow.validator';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $map = [];
        foreach ($container->findTaggedServiceIds(self::WORKFLOW_VALIDATOR_TAG) as $id => $attributes) {
            $definition = $container->findDefinition($id);

            if (!isset($attributes[0]['type'])) {
                $attributes[0]['type'] = Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1));
            }

            if (!isset($attributes[0]['manager'])) {
                throw new \InvalidArgumentException('Tagged Condition `' . $id . '` needs to have `manager` attribute.');
            }

            $manager = $container->getDefinition($attributes[0]['manager']);

            if (!$manager) {
                throw new \InvalidArgumentException(sprintf('Workflow Manager with identifier %s not found', $attributes[0]['manager']));
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];
            $priority = isset($attributes[0]['priority']) ? (int) $attributes[0]['priority'] : 0;

            $manager->addMethodCall('addValidator', [new Reference($id), $attributes[0]['type'], $priority]);
        }
    }
}

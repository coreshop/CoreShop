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

namespace CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterCheckoutStepPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $map = [];
        foreach ($container->findTaggedServiceIds('coreshop.registry.checkout.step') as $id => $attributes) {
            if (!isset($attributes[0]['type']) || !isset($attributes[0]['manager'])) {
                throw new \InvalidArgumentException('Tagged Condition `'.$id.'` needs to have `type` and `manager`.');
            }

            $manager = $container->getDefinition($attributes[0]['manager']);

            if (!$manager) {
                throw new \InvalidArgumentException(sprintf('Cart Manager with identifier %s not found', $attributes[0]['manager']));
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];
            $priority = isset($attributes[0]['priority']) ? (int) $attributes[0]['priority'] : 0;

            $manager->addMethodCall('addCheckoutStep', [new Reference($id), $priority]);
        }
    }
}

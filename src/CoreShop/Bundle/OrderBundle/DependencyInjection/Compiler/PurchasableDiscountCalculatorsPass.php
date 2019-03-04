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

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class PurchasableDiscountCalculatorsPass implements CompilerPassInterface
{
    public const PURCHASABLE_DISCOUNT_CALCULATOR_TAG = 'coreshop.order.purchasable.discount_calculator';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('coreshop.registry.order.purchasable.discount_calculators')) {
            return;
        }

        $registry = $container->getDefinition('coreshop.registry.order.purchasable.discount_calculators');

        $map = [];
        foreach ($container->findTaggedServiceIds(self::PURCHASABLE_DISCOUNT_CALCULATOR_TAG) as $id => $attributes) {
            $definition = $container->findDefinition($id);

            if (!isset($attributes[0]['type'])) {
                $attributes[0]['type'] = Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1, -9));
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];
            $registry->addMethodCall('register', [$attributes[0]['type'], $attributes[0]['priority'] ?? 1000, new Reference($id)]);
        }

        $container->setParameter('coreshop.order.purchasable.discount_calculators', $map);
    }
}

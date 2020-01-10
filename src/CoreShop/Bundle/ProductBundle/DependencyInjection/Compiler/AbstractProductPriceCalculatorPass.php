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

namespace CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractProductPriceCalculatorPass implements CompilerPassInterface
{
    /**
     * @return string
     */
    abstract protected function getRegistry();

    /**
     * @return string
     */
    abstract protected function getTag();

    /**
     * @return string
     */
    abstract protected function getParameter();

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->getRegistry())) {
            return;
        }

        $registry = $container->getDefinition($this->getRegistry());

        $map = [];
        foreach ($container->findTaggedServiceIds($this->getTag()) as $id => $attributes) {
            if (!isset($attributes[0]['priority']) || !isset($attributes[0]['type'])) {
                throw new \InvalidArgumentException('Tagged PriceCalculator `' . $id . '` needs to have `priority`, `type` attributes.');
            }

            $map[$attributes[0]['type']] = $attributes[0]['type'];
            $registry->addMethodCall('register', [$attributes[0]['type'], $attributes[0]['priority'], new Reference($id)]);
        }

        $container->setParameter($this->getParameter(), $map);
    }
}

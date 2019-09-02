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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler;

use CoreShop\Component\Resource\Metadata\RegistryInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

final class RegisterResourcesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $resources = $container->getParameter('coreshop.resources');
            $registry = $container->findDefinition(RegistryInterface::class);
        } catch (InvalidArgumentException $exception) {
            return;
        }

        foreach ($resources as $alias => $configuration) {
            $this->validateCoreShopModel($configuration['classes']['model']);
            $registry->addMethodCall('addFromAliasAndConfiguration', [$alias, $configuration]);
        }
    }

    /**
     * @param string $class
     */
    private function validateCoreShopModel($class)
    {
        if (!in_array(ResourceInterface::class, class_implements($class), true)) {
            throw new InvalidArgumentException(sprintf(
                'Class "%s" must implement "%s" to be registered as a Resource model.',
                $class,
                ResourceInterface::class
            ));
        }
    }
}

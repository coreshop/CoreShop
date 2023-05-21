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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ClassDefinitionPatchBundle\DependencyInjection;

use CoreShop\Bundle\ClassDefinitionPatchBundle\Patch;
use CoreShop\Bundle\ClassDefinitionPatchBundle\Patches;
use CoreShop\Bundle\ClassDefinitionPatchBundle\PatchField;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class CoreShopClassDefinitionPatchExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $patches = [];

        foreach ($configs['patches'] ?? [] as $className => $patch) {
            $fields = [];

            foreach ($patch['fields'] as $fieldName => $field) {
                $fieldId = sprintf('coreshop.patch.%s.field.%s', $className, $fieldName);

                $container->setDefinition(
                    $fieldId,
                    new Definition(PatchField::class, [
                        $fieldName,
                        $field['after'] ?? null,
                        $field['before'] ?? null,
                        $field['definition'] ?? null,
                    ]),
                );

                $fields[] = new Reference($fieldId);
            }

            $patches[$className] = new Definition(Patch::class, [
                $className,
                $patch['interface'] ?? null,
                $patch['parent_class'] ?? null,
                $patch['group'] ?? null,
                $patch['description'] ?? null,
                $patch['listing_parent_class'] ?? null,
                $patch['use_traits'] ?? null,
                $patch['listing_use_traits'] ?? null,
                $fields,
            ]);
        }

        $container->getDefinition(Patches::class)->setArgument(0, $patches);
    }
}

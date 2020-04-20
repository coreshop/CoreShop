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

namespace CoreShop\Bundle\ThemeBundle\DependencyInjection;

use CoreShop\Bundle\ThemeBundle\DependencyInjection\Compiler\CompositeThemeResolverPass;
use CoreShop\Bundle\ThemeBundle\Service\InheritanceLocator;
use CoreShop\Bundle\ThemeBundle\Service\PimcoreDocumentPropertyResolver;
use CoreShop\Bundle\ThemeBundle\Service\PimcoreSiteThemeResolver;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CoreShopThemeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        if (false === $config['default_resolvers']['pimcore_site']) {
            $container->removeDefinition(PimcoreSiteThemeResolver::class);
        }

        if (false === $config['default_resolvers']['pimcore_document_property']) {
            $container->removeDefinition(PimcoreDocumentPropertyResolver::class);
        }

        if (isset($config['inheritance']) && count($config['inheritance']) > 0) {
            $container->setParameter('coreshop.theme_bundle.inheritance', $config['inheritance']);

            $inheritanceLocator = new Definition(InheritanceLocator::class);
            $inheritanceLocator->setArguments([
                new Reference('kernel'),
                new Reference('liip_theme.active_theme'),
                '%kernel.root_dir%/Resources',
                [],
                '%liip_theme.path_patterns%',
                '%coreshop.theme_bundle.inheritance%'
            ]);
            $inheritanceLocator->setDecoratedService('liip_theme.file_locator');

            $container->setDefinition(InheritanceLocator::class, $inheritanceLocator);
        }

        $container
            ->registerForAutoconfiguration(ThemeResolverInterface::class)
            ->addTag(CompositeThemeResolverPass::THEME_RESOLVER_TAG);
    }
}

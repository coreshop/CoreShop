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

declare(strict_types=1);

namespace CoreShop\Bundle\ThemeBundle\DependencyInjection;

use CoreShop\Bundle\ThemeBundle\DependencyInjection\Compiler\CompositeThemeResolverPass;
use CoreShop\Bundle\ThemeBundle\Service\PimcoreDocumentPropertyResolver;
use CoreShop\Bundle\ThemeBundle\Service\PimcoreSiteThemeResolver;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
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

        $container
            ->registerForAutoconfiguration(ThemeResolverInterface::class)
            ->addTag(CompositeThemeResolverPass::THEME_RESOLVER_TAG);
    }
}

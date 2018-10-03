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

namespace CoreShop\Bundle\ResourceBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopResourceExtension extends AbstractModelExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yml');

        if ($config['translation']['enabled']) {
            $loader->load('services/integrations/translation.yml');

            $alias = new Alias($config['translation']['locale_provider']);
            $alias->setPublic(true);

            $container->setAlias('coreshop.translation_locale_provider', $alias);
        }

        if (array_key_exists('pimcore_admin', $config)) {
            $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);
        }

        if (!$container->hasParameter('coreshop.all.pimcore_classes')) {
            $container->setParameter('coreshop.all.pimcore_classes', []);
        }

        if (!$container->hasParameter('coreshop.all.stack')) {
            $container->setParameter('coreshop.all.stack', []);
        }

        $this->loadPersistence($config['drivers'], $config['resources'], $loader);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $fosRestConfig = [
            'format_listener' => [
                'rules' => [
                    [
                        'path' => '^/admin/coreshop',
                        'priorities' => ['json', 'xml'],
                        'fallback_format' => 'json',
                        'prefer_extension' => true
                    ],
                    [
                        'path' => '^/',
                        'stop' => true
                    ]
                ]
            ]
        ];

        $container->prependExtensionConfig('fos_rest', $fosRestConfig);
    }

    private function loadPersistence(array $drivers, array $resources, LoaderInterface $loader)
    {
        foreach ($resources as $alias => $resource) {
            if (!in_array($resource['driver'], $drivers, true)) {
                throw new InvalidArgumentException(sprintf(
                    'Resource "%s" uses driver "%s", but this driver has not been enabled.',
                    $alias,
                    $resource['driver']
                ));
            }
        }

        foreach ($drivers as $driver) {
            $loader->load(sprintf('services/integrations/%s.yml', $driver));
        }
    }
}

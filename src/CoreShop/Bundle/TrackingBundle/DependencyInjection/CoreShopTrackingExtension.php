<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\TrackingBundle\DependencyInjection;

use CoreShop\Bundle\TrackingBundle\DependencyInjection\Compiler\TrackerPass;
use CoreShop\Bundle\TrackingBundle\DependencyInjection\Compiler\TrackingExtractorPass;
use CoreShop\Component\Tracking\Extractor\TrackingExtractorInterface;
use CoreShop\Component\Tracking\Tracker\TrackerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class CoreShopTrackingExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this->configureTrackers($config, $container);

        $container
            ->registerForAutoconfiguration(TrackerInterface::class)
            ->addTag(TrackerPass::TRACKER_TAG);

        $container
            ->registerForAutoconfiguration(TrackingExtractorInterface::class)
            ->addTag(TrackingExtractorPass::TRACKING_EXTRACTOR_TAG);
    }

    protected function configureTrackers(array $config, ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds(TrackerPass::TRACKER_TAG) as $id => $attributes) {
            foreach ($attributes as $tag) {
                $definition = $container->findDefinition($id);

                $type = $tag['type'] ?? Container::underscore(substr(strrchr($definition->getClass(), '\\'), 1));

                if (!array_key_exists($type, $config['trackers'])) {
                    $container->getDefinition($id)
                        ->addMethodCall('setEnabled', [false]);
                } else {
                    $container->getDefinition($id)
                        ->addMethodCall('setEnabled', [$config['trackers'][$type]['enabled']]);
                }
            }
        }
    }
}

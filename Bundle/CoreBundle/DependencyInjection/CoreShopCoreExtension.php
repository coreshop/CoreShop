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

namespace CoreShop\Bundle\CoreBundle\DependencyInjection;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopCoreExtension extends AbstractModelExtension implements PrependExtensionInterface
{
    /**
     * @var array
     */
    private static $bundles = [
        'coreshop_address',
        'coreshop_currency',
        'coreshop_customer',
        'coreshop_index',
        'coreshop_order',
        'coreshop_product',
        'coreshop_payment',
        'coreshop_payum',
        'coreshop_product',
        'coreshop_rule',
        'coreshop_sequence',
        'coreshop_store',
        'coreshop_shipping',
        'coreshop_taxation',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('coreshop', $config['driver'], [], $container);

        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);

        foreach ($container->getExtensions() as $name => $extension) {
            if (in_array($name, self::$bundles, true)) {
                $container->prependExtensionConfig($name, ['driver' => $config['driver']]);
            }
        }
    }
}

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
use CoreShop\Component\Order\Checkout\CheckoutManagerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

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
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('coreshop', $config['driver'], [], $container);

        if (array_key_exists('pimcore_admin', $config)) {
            $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);
        }

        $loader->load('services.yml');

        if (array_key_exists('checkout', $config)) {
            $this->registerCheckout($container, $config['checkout']);
        }

        if (array_key_exists('checkout_manager', $config)) {
            $alias = new Alias(sprintf('coreshop.checkout_manager.%s', $config['checkout_manager']));
            $container->setAlias('coreshop.checkout_manager', $alias);
        }
        else {
            throw new \InvalidArgumentException('No valid Checkout Manager has been configured!');
        }
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

    /**
     * @param ContainerBuilder $container
     * @param $config
     */
    private function registerCheckout(ContainerBuilder $container, $config)
    {
        $availableCheckoutManagers = [];

        foreach ($config as $checkoutManager => $typeConfiguration) {
            $checkoutManagerId = sprintf(sprintf('coreshop.checkout_manager.%s', $checkoutManager));
            $managerClass = $typeConfiguration['manager'];

            Assert::classExists($managerClass);
            Assert::implementsInterface($managerClass, CheckoutManagerInterface::class);

            $definition = new Definition($managerClass);

            foreach ($typeConfiguration['steps'] as $step) {
                $definition->addMethodCall('addCheckoutStep', [new Reference($step['step']), $step['priority']]);
            }

            $container->setDefinition($checkoutManagerId, $definition);

            $availableCheckoutManagers[] = $checkoutManagerId;
        }

        $container->setParameter('coreshop.checkout_managers', $availableCheckoutManagers);
    }
}

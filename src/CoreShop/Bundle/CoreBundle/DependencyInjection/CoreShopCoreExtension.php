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

namespace CoreShop\Bundle\CoreBundle\DependencyInjection;

use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterPortletsPass;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterReportsPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Core\Portlet\PortletInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Order\Checkout\DefaultCheckoutManagerFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

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

        $this->registerResources('coreshop', $config['driver'], $config['resources'], $container);

        if (array_key_exists('pimcore_admin', $config)) {
            $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);
        }

        $container->setParameter('coreshop.after_logout_redirect_route', $config['after_logout_redirect_route']);

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        $loader->load('services.yml');

        if (array_key_exists('checkout', $config)) {
            $this->registerCheckout($container, $config['checkout']);
        }

        if (array_key_exists('checkout_manager_factory', $config)) {
            $alias = new Alias(sprintf('coreshop.checkout_manager.factory.%s', $config['checkout_manager_factory']));
            $alias->setPublic(true);

            $container->setAlias('coreshop.checkout_manager.factory', $alias);
        } else {
            throw new \InvalidArgumentException('No valid Checkout Manager has been configured!');
        }

        $container
            ->registerForAutoconfiguration(PortletInterface::class)
            ->addTag(RegisterPortletsPass::PORTLET_TAG);
        $container
            ->registerForAutoconfiguration(ReportInterface::class)
            ->addTag(RegisterReportsPass::REPORT_TAG);
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
     * @param array            $config
     */
    private function registerCheckout(ContainerBuilder $container, $config)
    {
        $availableCheckoutManagerFactories = [];

        foreach ($config as $checkoutIdentifier => $typeConfiguration) {
            $stepsLocatorId = sprintf('coreshop.checkout_manager.steps.%s', $checkoutIdentifier);
            $checkoutManagerFactoryId = sprintf('coreshop.checkout_manager.factory.%s', $checkoutIdentifier);

            $services = [];
            $priorityMap = [];

            foreach ($typeConfiguration['steps'] as $identifier => $step) {
                $services[$identifier] = new Reference($step['step']);
                $priorityMap[$identifier] = $step['priority'];
            }

            $stepsLocator = new Definition(ServiceLocator::class, [$services]);
            $stepsLocator->addTag('container.service_locator');
            $container->setDefinition($stepsLocatorId, $stepsLocator);

            $checkoutManagerFactory = new Definition(DefaultCheckoutManagerFactory::class, [
                new Reference($stepsLocatorId),
                $priorityMap,
            ]);

            $container->setDefinition($checkoutManagerFactoryId, $checkoutManagerFactory);

            $availableCheckoutManagerFactories[] = $checkoutManagerFactoryId;
        }

        $container->setParameter('coreshop.checkout_managers', $availableCheckoutManagerFactories);
    }
}

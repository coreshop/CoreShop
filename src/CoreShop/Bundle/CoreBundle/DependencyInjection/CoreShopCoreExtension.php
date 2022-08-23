<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\DependencyInjection;

use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterPortletsPass;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterReportsPass;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Core\Portlet\PortletInterface;
use CoreShop\Component\Core\Report\ReportInterface;
use CoreShop\Component\Order\Checkout\CheckoutManagerFactoryInterface;
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
    private static array $bundles = [
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

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('coreshop.all.dependant.bundles', []);

        $this->registerResources('coreshop', CoreShopResourceBundle::DRIVER_DOCTRINE_ORM, $configs['resources'], $container);

        if (array_key_exists('pimcore_admin', $configs)) {
            $this->registerPimcoreResources('coreshop', $configs['pimcore_admin'], $container);
        }

        $container->setParameter('coreshop.after_logout_redirect_route', $configs['after_logout_redirect_route']);

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        $loader->load('services.yml');

        $env = (string)$container->getParameter('kernel.environment');
        if (str_contains($env, 'test')) {
            $loader->load('services_test.yml');
        }

        if (array_key_exists('checkout', $configs)) {
            $this->registerCheckout($container, $configs['checkout']);
        }

        if (array_key_exists('checkout_manager_factory', $configs)) {
            $alias = new Alias(sprintf('coreshop.checkout_manager.factory.%s', $configs['checkout_manager_factory']));
            $alias->setPublic(true);

            $container->setAlias('coreshop.checkout_manager.factory', $alias);
            $container->setAlias(CheckoutManagerFactoryInterface::class, $alias);
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

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        foreach ($container->getExtensions() as $name => $extension) {
            if (in_array($name, self::$bundles, true)) {
                $container->prependExtensionConfig($name, ['driver' => $configs['driver']]);
            }
        }
    }

    private function registerCheckout(ContainerBuilder $container, array $configs): void
    {
        $availableCheckoutManagerFactories = [];

        foreach ($configs as $checkoutIdentifier => $typeConfiguration) {
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

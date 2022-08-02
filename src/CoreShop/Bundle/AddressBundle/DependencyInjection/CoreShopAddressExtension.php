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

namespace CoreShop\Bundle\AddressBundle\DependencyInjection;

use CoreShop\Bundle\AddressBundle\DependencyInjection\Compiler\CompositeCountryContextPass;
use CoreShop\Bundle\AddressBundle\DependencyInjection\Compiler\CompositeRequestResolverPass;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Address\Context\CountryContextInterface;
use CoreShop\Component\Address\Context\RequestBased\RequestResolverInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopAddressExtension extends AbstractModelExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configs = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('coreshop', CoreShopResourceBundle::DRIVER_DOCTRINE_ORM, $configs['resources'], $container);
        $this->registerPimcoreModels('coreshop', $configs['pimcore'], $container);

        if (array_key_exists('pimcore_admin', $configs)) {
            $this->registerPimcoreResources('coreshop', $configs['pimcore_admin'], $container);
        }

        if (array_key_exists('stack', $configs)) {
            $this->registerStack('coreshop', $configs['stack'], $container);
        }

        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('PimcoreDataHubBundle', $bundles)) {
            $loader->load('services/data_hub.yml');
        }

        $loader->load('services.yml');

        $container
            ->registerForAutoconfiguration(CountryContextInterface::class)
            ->addTag(CompositeCountryContextPass::COUNTRY_CONTEXT_SERVICE_TAG);
        $container
            ->registerForAutoconfiguration(RequestResolverInterface::class)
            ->addTag(CompositeRequestResolverPass::COUNTRY_REQUEST_RESOLVER_SERVICE_TAG);
    }
}

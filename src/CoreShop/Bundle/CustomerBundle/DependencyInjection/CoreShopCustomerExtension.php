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

namespace CoreShop\Bundle\CustomerBundle\DependencyInjection;

use CoreShop\Bundle\CustomerBundle\DependencyInjection\Compiler\CompositeCustomerContextPass;
use CoreShop\Bundle\CustomerBundle\DependencyInjection\Compiler\CompositeRequestResolverPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractModelExtension;
use CoreShop\Component\Customer\Context\CustomerContextInterface;
use CoreShop\Component\Customer\Context\RequestBased\RequestResolverInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CoreShopCustomerExtension extends AbstractModelExtension
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        //$this->registerResources('coreshop', CoreShopResourceBundle::DRIVER_DOCTRINE_ORM, $config['resources'], $container);
        $this->registerPimcoreModels('coreshop', $config['pimcore'], $container);
        $this->registerPimcoreResources('coreshop', $config['pimcore_admin'], $container);
        $this->registerStack('coreshop', $config['stack'], $container);

        $container->setParameter('coreshop.customer.security.login_identifier', $config['login_identifier']);

        $loader->load('services.yml');

        $container
            ->registerForAutoconfiguration(CustomerContextInterface::class)
            ->addTag(CompositeCustomerContextPass::CUSTOMER_CONTEXT_SERVICE_TAG);
        $container
            ->registerForAutoconfiguration(RequestResolverInterface::class)
            ->addTag(CompositeRequestResolverPass::CUSTOMER_REQUEST_RESOLVER_SERVICE_TAG);
    }
}

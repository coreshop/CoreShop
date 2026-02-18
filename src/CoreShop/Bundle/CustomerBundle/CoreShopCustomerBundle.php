<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CustomerBundle;

use CoreShop\Bundle\CustomerBundle\DependencyInjection\Compiler\CompositeCustomerContextPass;
use CoreShop\Bundle\CustomerBundle\DependencyInjection\Compiler\CompositeRequestResolverPass;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopCustomerBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        parent::registerDependentBundles($collection);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompositeCustomerContextPass());
        $container->addCompilerPass(new CompositeRequestResolverPass());
    }

    protected function getModelNamespace(): string
    {
        return 'CoreShop\Component\Customer\Model';
    }
}

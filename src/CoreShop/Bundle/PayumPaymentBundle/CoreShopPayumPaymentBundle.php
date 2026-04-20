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

namespace CoreShop\Bundle\PayumPaymentBundle;

use CoreShop\Bundle\PaymentBundle\CoreShopPaymentBundle;
use CoreShop\Bundle\PayumPaymentBundle\DependencyInjection\Compiler\RegisterGatewayConfigTypePass;
use CoreShop\Bundle\PayumPaymentBundle\DependencyInjection\Compiler\RegisterPaymentSettingsFormsPass;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\ComposerPackageBundleInterface;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CoreShopPayumPaymentBundle extends AbstractResourceBundle implements PimcoreBundleInterface, ComposerPackageBundleInterface
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

        $collection->addBundle(new CoreShopPaymentBundle(), 2200);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterGatewayConfigTypePass());
        $container->addCompilerPass(new RegisterPaymentSettingsFormsPass());
    }

    protected function getModelNamespace(): string
    {
        return 'CoreShop\Component\PayumPayment\Model';
    }

    public function getPackageName(): string
    {
        return 'coreshop/payum-payment-bundle';
    }

    public function getNiceName(): string
    {
        return 'CoreShop - Payum Payment';
    }

    public function getDescription(): string
    {
        return 'CoreShop - Payum Payment Bundle';
    }

    public function getInstaller(): ?InstallerInterface
    {
        return null;
    }
}

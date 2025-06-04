<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PayumBundle;

use CoreShop\Bundle\OrderBundle\CoreShopOrderBundle;
use CoreShop\Bundle\PayumBundle\DependencyInjection\Compiler\PayumReplyToSymfonyPass;
use CoreShop\Bundle\PayumPaymentBundle\CoreShopPayumPaymentBundle;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use Payum\Bundle\PayumBundle\PayumBundle;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopPayumBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new PayumReplyToSymfonyPass());
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        parent::registerDependentBundles($collection);

        $collection->addBundle(new CoreShopOrderBundle(), 3200);
        $collection->addBundle(new CoreShopPayumPaymentBundle(), 2100);
        $collection->addBundle(new PayumBundle(), 1300);
    }

    protected function getModelNamespace(): string
    {
        return 'CoreShop\Component\PayumPayment\Model';
    }
}

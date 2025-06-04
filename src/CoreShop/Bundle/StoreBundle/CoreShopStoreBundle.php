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

namespace CoreShop\Bundle\StoreBundle;

use CoreShop\Bundle\CurrencyBundle\CoreShopCurrencyBundle;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\StoreBundle\DependencyInjection\Compiler\CompositeRequestResolverPass;
use CoreShop\Bundle\StoreBundle\DependencyInjection\Compiler\CompositeStoreContextPass;
use CoreShop\Bundle\ThemeBundle\CoreShopThemeBundle;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopStoreBundle extends AbstractResourceBundle
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

        $container->addCompilerPass(new CompositeStoreContextPass());
        $container->addCompilerPass(new CompositeRequestResolverPass());
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        parent::registerDependentBundles($collection);

        $collection->addBundle(new CoreShopCurrencyBundle(), 2700);
        $collection->addBundle(new CoreShopThemeBundle(), 2800);
    }

    protected function getModelNamespace(): string
    {
        return 'CoreShop\Component\Store\Model';
    }
}

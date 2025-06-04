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

namespace CoreShop\Bundle\ShippingBundle;

use CoreShop\Bundle\AddressBundle\CoreShopAddressBundle;
use CoreShop\Bundle\CurrencyBundle\CoreShopCurrencyBundle;
use CoreShop\Bundle\MoneyBundle\CoreShopMoneyBundle;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\RuleBundle\CoreShopRuleBundle;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\CompositeShippableValidatorPass;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\ShippingPriceCalculatorsPass;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\ShippingRuleActionPass;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\ShippingRuleConditionPass;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\ShippingTaxCalculationStrategyPass;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopShippingBundle extends AbstractResourceBundle
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

        $container->addCompilerPass(new ShippingRuleConditionPass());
        $container->addCompilerPass(new ShippingRuleActionPass());
        $container->addCompilerPass(new ShippingPriceCalculatorsPass());
        $container->addCompilerPass(new CompositeShippableValidatorPass());
        $container->addCompilerPass(new ShippingTaxCalculationStrategyPass());
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        parent::registerDependentBundles($collection);

        $collection->addBundle(new CoreShopAddressBundle(), 2800);
        $collection->addBundle(new CoreShopRuleBundle(), 3500);
        $collection->addBundle(new CoreShopMoneyBundle(), 3600);
        $collection->addBundle(new CoreShopCurrencyBundle(), 2700);
    }

    protected function getModelNamespace(): string
    {
        return 'CoreShop\Component\Shipping\Model';
    }
}

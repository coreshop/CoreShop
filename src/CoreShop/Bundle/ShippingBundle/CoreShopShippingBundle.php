<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ShippingRuleConditionPass());
        $container->addCompilerPass(new ShippingRuleActionPass());
        $container->addCompilerPass(new ShippingPriceCalculatorsPass());
        $container->addCompilerPass(new CompositeShippableValidatorPass());
        $container->addCompilerPass(new ShippingTaxCalculationStrategyPass());
    }

    /**
     * {@inheritdoc}
     */
    public static function registerDependentBundles(BundleCollection $collection)
    {
        parent::registerDependentBundles($collection);

        $collection->addBundle(new CoreShopAddressBundle(), 2800);
        $collection->addBundle(new CoreShopRuleBundle(), 3500);
        $collection->addBundle(new CoreShopMoneyBundle(), 3600);
        $collection->addBundle(new CoreShopCurrencyBundle(), 2700);
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Shipping\Model';
    }
}

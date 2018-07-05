<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ProductBundle;

use CoreShop\Bundle\MoneyBundle\CoreShopMoneyBundle;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductDiscountCalculatorsPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductDiscountPriceCalculatorsPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductPriceRuleActionPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductPriceRuleConditionPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductRetailPriceCalculatorsPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductSpecificPriceRuleActionPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductSpecificPriceRuleConditionPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductValidPriceRuleFetcherPass;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\RuleBundle\CoreShopRuleBundle;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopProductBundle extends AbstractResourceBundle
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
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProductPriceRuleConditionPass());
        $container->addCompilerPass(new ProductPriceRuleActionPass());
        $container->addCompilerPass(new ProductSpecificPriceRuleConditionPass());
        $container->addCompilerPass(new ProductSpecificPriceRuleActionPass());
        $container->addCompilerPass(new ProductValidPriceRuleFetcherPass());

        $container->addCompilerPass(new ProductRetailPriceCalculatorsPass());
        $container->addCompilerPass(new ProductDiscountPriceCalculatorsPass());
        $container->addCompilerPass(new ProductDiscountCalculatorsPass());
    }

    /**
     * {@inheritdoc}
     */
    public static function registerDependentBundles(BundleCollection $collection)
    {
        parent::registerDependentBundles($collection);

        $collection->addBundle(new CoreShopMoneyBundle(), 3600);
        $collection->addBundle(new CoreShopRuleBundle(), 3500);
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Product\Model';
    }
}

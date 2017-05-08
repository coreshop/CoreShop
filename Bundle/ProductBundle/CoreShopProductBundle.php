<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ProductBundle;

use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductPriceCalculatorsPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductPriceRuleActionPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductPriceRuleConditionPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductSpecificPriceRuleActionPass;
use CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler\ProductSpecificPriceRuleConditionPass;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
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

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProductPriceRuleConditionPass());
        $container->addCompilerPass(new ProductPriceRuleActionPass());
        $container->addCompilerPass(new ProductSpecificPriceRuleConditionPass());
        $container->addCompilerPass(new ProductSpecificPriceRuleActionPass());
        $container->addCompilerPass(new ProductPriceCalculatorsPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Product\Model';
    }
}

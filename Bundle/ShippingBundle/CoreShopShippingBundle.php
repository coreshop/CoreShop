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
 *
*/

namespace CoreShop\Bundle\ShippingBundle;

use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\ShippingPriceCalculatorsPass;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\ShippingRuleActionPass;
use CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler\ShippingRuleConditionPass;
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

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ShippingRuleConditionPass());
        $container->addCompilerPass(new ShippingRuleActionPass());
        $container->addCompilerPass(new ShippingPriceCalculatorsPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Shipping\Model';
    }
}

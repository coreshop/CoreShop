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

namespace CoreShop\Bundle\OrderBundle;

use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\CartPriceRuleActionPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\CartPriceRuleConditionPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasableDiscountCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasableDiscountPriceCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasablePriceCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\PurchasableRetailPriceCalculatorsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterCartContextsPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterCartProcessorPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterWorkflowManagerPass;
use CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterWorkflowValidatorPass;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopOrderBundle extends AbstractResourceBundle
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

        $container->addCompilerPass(new CartPriceRuleActionPass());
        $container->addCompilerPass(new CartPriceRuleConditionPass());
        $container->addCompilerPass(new RegisterWorkflowValidatorPass());
        $container->addCompilerPass(new RegisterCartProcessorPass());
        $container->addCompilerPass(new PurchasablePriceCalculatorsPass());
        $container->addCompilerPass(new PurchasableDiscountCalculatorsPass());
        $container->addCompilerPass(new RegisterCartContextsPass());
        $container->addCompilerPass(new PurchasableDiscountPriceCalculatorsPass());
        $container->addCompilerPass(new PurchasableRetailPriceCalculatorsPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Order\Model';
    }
}

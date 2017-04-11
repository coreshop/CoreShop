<?php

namespace CoreShop\Bundle\ShippingBundle;

use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
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
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Shipping\Model';
    }
}

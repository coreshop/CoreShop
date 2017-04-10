<?php

namespace CoreShop\Bundle\AddressBundle;

use CoreShop\Bundle\AddressBundle\DependencyInjection\Compiler\CompositeCountryContextPass;
use CoreShop\Bundle\AddressBundle\DependencyInjection\Compiler\CompositeRequestResolverPass;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopAddressBundle extends AbstractResourceBundle
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

        $container->addCompilerPass(new CompositeCountryContextPass());
        $container->addCompilerPass(new CompositeRequestResolverPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Address\Model';
    }
}

<?php

namespace CoreShop\Bundle\IndexBundle;

use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterIndexWorkerPass;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterColumnTypePass;

final class CoreShopIndexBundle extends AbstractResourceBundle
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

        $container->addCompilerPass(new RegisterColumnTypePass());
        $container->addCompilerPass(new RegisterIndexWorkerPass());
    }


    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Index\Model';
    }
}

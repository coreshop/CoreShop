<?php

namespace CoreShop\Bundle\AdminBundle\Pimcore;

use Pimcore\Bundle\AdminBundle\Pimcore\InstallHelper;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Model\Object\Service;

class Install extends AbstractInstaller
{
    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $installFileDir = __DIR__ . '/../Resources/install';

        $classes = [
            'CoreShopCategory',
            'CoreShopProduct',
            'CoreShopCart',
            'CoreShopCartItem',
            'CoreShopCustomer',
            'CoreShopCustomerGroup',
            'CoreShopOrderItem',
            'CoreShopPayment',
            'CoreShopOrder',
            'CoreShopAddress'
        ];

        $folders = [
            '/coreshop/cart',
            '/coreshop/product',
            '/coreshop/customer',
            '/coreshop/order'
        ];

        InstallHelper::runDoctrineOrmSchemaUpdate();

        foreach ($folders as $folder) {
            Service::createFolderByPath($folder);
        }

        foreach ($classes as $class) {
            InstallHelper::createClass($installFileDir . sprintf('/class_%s_export.json', $class), $class);
        }

        //TODO: Could this task actually run a sub task to install the database?
        //TODO: Create Routes, however we actually use routes then, currently we use symfony routing stuff
        //TODO: Create Configuration, however configuration looks like in the future, probably just a Doctirne Entity
        //TODO: Install Order Workflow
        //TODO: Install E-Mail Documents
        //TODO: Install Mail Rules, not implemented yet
        //TODO: Install Bricks
        //TODO: Install Field Collections
        //TODO: Install Customer Service Stuff, not implemented yet
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isInstalled()
    {
        return class_exists('Pimcore\Model\Object\CoreShopProduct'); //TODO: Haha :D, best way, isn't it?
    }

    /**
     * {@inheritdoc}
     */
    public function canBeInstalled()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUninstalled()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function needsReloadAfterInstall()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canBeUpdated()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function update()
    {
        InstallHelper::runDoctrineOrmSchemaUpdate();
    }
}
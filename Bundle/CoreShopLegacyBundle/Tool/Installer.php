<?php

namespace CoreShop\Bundle\CoreShopLegacyBundle\Tool;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Configuration;
use CoreShop\Bundle\CoreShopLegacyBundle\Plugin\LegacyInstaller;
use Pimcore\Config;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Logger;

final class Installer extends AbstractInstaller
{
    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $legacyInstaller = new LegacyInstaller();
        $legacyInstaller->executeSQL('CoreShop');
        $legacyInstaller->executeSQL('CoreShop-States');
        $legacyInstaller->createConfig();
        $legacyInstaller->fullInstall();
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
        $config = Configuration::get('SYSTEM.ISINSTALLED');

        if (!is_null($config)) {
            return true;
        }

        return false;
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
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canBeUpdated()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function update()
    {
        throw new \Exception("Not Supported!");
    }
}
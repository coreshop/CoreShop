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

namespace CoreShop\Bundle\CoreBundle\Pimcore;

use Pimcore\Bundle\AdminBundle\Pimcore\InstallHelper;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class PimcoreInstaller extends AbstractInstaller
{
    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $kernel = \Pimcore::getKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $options = ['command' => 'coreshop:install'];
        $options = array_merge($options, ['--no-interaction' => true]);
        $application->run(new ArrayInput($options));

        //TODO: Create Configuration, however configuration looks like in the future, probably just a Doctrine Entity -> therefore Fixtures!
        //TODO: Install Bricks
        //TODO: Install Customer Service Stuff, not implemented yet -> done via Fixtures
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
     * {@inheritdoc}
     */
    public function canBeUpdated()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function update()
    {
        //InstallHelper::runDoctrineOrmSchemaUpdate();
    }
}

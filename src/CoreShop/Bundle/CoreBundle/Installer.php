<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle;

use Doctrine\DBAL\Migrations\Version;
use Doctrine\DBAL\Schema\Schema;
use Pimcore\Extension\Bundle\Installer\MigrationInstaller;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class Installer extends MigrationInstaller
{
    /**
     * @var bool
     */
    protected $runUpdateAfterInstall = false;

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion(): string
    {
        return '00000001';
    }

    protected function beforeInstallMigration()
    {
        $kernel = \Pimcore::getKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $options = ['command' => 'coreshop:install'];
        $options = array_merge($options, ['--no-interaction' => true, '--application-name coreshop']);
        $application->run(new ArrayInput($options));
    }

    /**
     * {@inheritdoc}
     */
    public function migrateInstall(Schema $schema, Version $version)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function migrateUninstall(Schema $schema, Version $version)
    {
        //TODO: Uninstalling of CoreShop eg. dropping all tables
    }
}

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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle;

use Doctrine\Migrations\Metadata\AvailableMigration;
use Pimcore\Console\Application;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;

class Installer extends SettingsStoreAwareInstaller
{
    public function __construct(protected KernelInterface $kernel)
    {
        parent::__construct($this->kernel->getBundle('CoreShopCoreBundle'));
    }

    public function getLastMigrationVersionClassName(): ?string
    {
        $this->migrationRepository->setPrefix($this->bundle->getNamespace());
        $this->tableMetadataStorage->setPrefix($this->bundle->getNamespace());

        $migrations = $this->dependencyFactory->getMigrationRepository()->getMigrations();

        if (count($migrations) > 0) {
            /**
             * @var AvailableMigration $lastMigration
             */
            $lastMigration = $migrations->getItems()[count($migrations) - 1];

            return get_class($lastMigration->getMigration());
        }

        return null;
    }

    public function markAllMigrationsInstalled(): void
    {
        $this->markInstalled();
    }

    public function install(): void
    {
        /** @psalm-suppress InternalClass, InternalMethod */
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $options = ['command' => 'coreshop:install'];
        $options = array_merge($options, ['--no-interaction' => true, '--application-name coreshop']);
        $application->run(new ArrayInput($options));

        parent::install();
    }
}

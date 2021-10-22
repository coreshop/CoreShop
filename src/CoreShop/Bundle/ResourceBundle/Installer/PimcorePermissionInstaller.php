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

namespace CoreShop\Bundle\ResourceBundle\Installer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Pimcore\Model\User\Permission;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class PimcorePermissionInstaller implements ResourceInstallerInterface
{
    public function __construct(private KernelInterface $kernel, private Connection $connection)
    {
    }

    public function installResources(OutputInterface $output, string $applicationName = null, array $options = []): void
    {
        $parameter = $applicationName ? sprintf('%s.permissions', $applicationName) : 'coreshop.all.permissions';

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $permissionGroups = $this->kernel->getContainer()->getParameter($parameter);

            if ($parameter !== 'coreshop.all.permissions') {
                if (null !== $applicationName) {
                    $permissionGroups = [
                        $applicationName => $permissionGroups,
                    ];
                } else {
                    $permissionGroups = [
                        'all' => $permissionGroups,
                    ];
                }
            }

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
            $progress->start(count($permissionGroups, \COUNT_RECURSIVE));

            $columns = array_map(function (Column $column) {
                return $column->getName();
            }, $this->connection->getSchemaManager()->listTableColumns('users_permission_definitions'));

            foreach ($permissionGroups as $group => $permissions) {
                foreach ($permissions as $permission) {
                    $progress->setMessage(sprintf('Install Permission %s', $permission));

                    $permissionDefinition = Permission\Definition::getByKey($permission);

                    if (!$permissionDefinition instanceof Permission\Definition) {
                        if (in_array('category', $columns, true)) {
                            $this->connection->insert('users_permission_definitions', [
                                'key' => $permission,
                                'category' => sprintf('coreshop_permission_group_%s', $group),
                            ]);
                        } else {
                            $this->connection->insert('users_permission_definitions', [
                                'key' => $permission,
                            ]);
                        }
                    }

                    $progress->advance();
                }
            }

            $progress->finish();
            $progress->clear();

            $output->writeln('  - <info>Permissions have been installed successfully</info>');
        }
    }
}

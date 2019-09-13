<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Installer;

use Doctrine\DBAL\Connection;
use Pimcore\Model\User\Permission;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class PimcorePermissionInstaller implements ResourceInstallerInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param KernelInterface $kernelm
     * @param Connection      $connection
     */
    public function __construct(KernelInterface $kernel, Connection $connection)
    {
        $this->kernel = $kernel;
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output, $applicationName = null, $options = [])
    {
        $parameter = $applicationName ? sprintf('%s.permissions', $applicationName) : 'coreshop.all.permissions';

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $permissionGroups = $this->kernel->getContainer()->getParameter($parameter);

            if ($parameter !== 'coreshop.all.permissions') {
                $permissionGroups = [
                    $applicationName => $permissionGroups
                ];
            }

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
            $progress->start(count($permissionGroups, COUNT_RECURSIVE));

            foreach ($permissionGroups as $group => $permissions) {
                foreach ($permissions as $permission) {
                    $progress->setMessage(sprintf('<error>Install Permission %s</error>', $permission));

                    $permissionDefinition = Permission\Definition::getByKey($permission);

                    if (!$permissionDefinition instanceof Permission\Definition) {
                        $permissionDefinition = new Permission\Definition();

                        //Pimcore with < 6.2 doesn't persist the category with the object... (https://github.com/pimcore/pimcore/pull/4978)
                        if (method_exists($permissionDefinition, 'setCategory')) {
                            $permissionDefinition->setCategory($group);

                            $this->connection->insert('users_permission_definitions', [
                                'key' => $permission,
                                'category' => sprintf('coreshop_permission_group_%s', $group)
                            ]);
                        }
                        else {
                            $permissionDefinition->setKey($permission);
                            $permissionDefinition->save();
                        }
                    }

                    $progress->advance();
                }
            }

            $progress->finish();
        }
    }
}

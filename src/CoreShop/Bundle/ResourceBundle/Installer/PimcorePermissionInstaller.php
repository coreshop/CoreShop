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

namespace CoreShop\Bundle\ResourceBundle\Installer;

use Pimcore\Model\User\Permission;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class PimcorePermissionInstaller implements ResourceInstallerInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**<
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output, $applicationName = null, $options = [])
    {
        $parameter = $applicationName ? sprintf('%s.permissions', $applicationName) : 'coreshop.all.permissions';

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $permissions = $this->kernel->getContainer()->getParameter($parameter);

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
            $progress->start(count($permissions));

            foreach ($permissions as $permission) {
                $progress->setMessage(sprintf('<error>Install Permission %s</error>', $permission));

                $permissionDefinition = Permission\Definition::getByKey($permission);

                if (!$permissionDefinition instanceof Permission\Definition) {
                    $permissionDefinition = new Permission\Definition();
                    $permissionDefinition->setKey($permission);
                    $permissionDefinition->save();
                }

                $progress->advance();
            }

            $progress->finish();
        }
    }
}
<?php

namespace CoreShop\Bundle\ResourceBundle\Installer;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\User\Permission;

final class PimcorePermissionInstaller implements ResourceInstallerInterface
{
    /**
     * @var string[]
     */
    protected $permissions;

    /**<
     * @param $permissions
     */
    public function __construct($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output)
    {
        $progress = new ProgressBar($output);
        $progress->setBarCharacter('<info>░</info>');
        $progress->setEmptyBarCharacter(' ');
        $progress->setProgressCharacter('<comment>░</comment>');
        $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $progress->start(count($this->permissions));

        foreach ($this->permissions as $permission) {
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
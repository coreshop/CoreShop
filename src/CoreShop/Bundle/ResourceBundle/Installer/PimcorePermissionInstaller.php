<?php

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
    public function installResources(OutputInterface $output, $applicationName = null)
    {
        $parameter = $applicationName ? sprintf('%s.permissions', $applicationName) : 'coreshop.resource.permissions';

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
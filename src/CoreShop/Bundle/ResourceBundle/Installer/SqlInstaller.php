<?php

namespace CoreShop\Bundle\ResourceBundle\Installer;

use Pimcore\Db;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class SqlInstaller implements ResourceInstallerInterface
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
        $parameter = $applicationName ? sprintf('%s.application.pimcore.admin.install.sql', $applicationName) : 'resources.admin.install.sql';

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $sqlFilesToExecute = $this->kernel->getContainer()->getParameter($parameter);

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            $db = Db::get();

            $progress->start(count($sqlFilesToExecute));

            foreach ($sqlFilesToExecute as $sqlFile) {
                $progress->setMessage(sprintf('<error>Execute SQL File %s</error>', $sqlFile));

                $db->query(file_get_contents($this->kernel->locateResource($sqlFile)));

                $progress->advance();
            }

            $progress->finish();
        }
    }
}
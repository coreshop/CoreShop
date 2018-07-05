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

use Pimcore\Db;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class SqlInstaller implements ResourceInstallerInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

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
        $parameter = $applicationName ? sprintf('%s.pimcore.admin.install.sql', $applicationName) : 'coreshop.all.pimcore.admin.install.sql';

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

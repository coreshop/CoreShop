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

use Pimcore\Db;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class SqlInstaller implements ResourceInstallerInterface
{
    public function __construct(private KernelInterface $kernel)
    {
    }

    public function installResources(OutputInterface $output, string $applicationName = null, array $options = []): void
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
                $progress->setMessage(sprintf('<info>Execute SQL File %s</info>', $sqlFile));

                $db->executeQuery(file_get_contents($this->kernel->locateResource($sqlFile)));

                $progress->advance();
            }

            $progress->finish();
            $progress->clear();

            $output->writeln('  - <info>SQLs have been installed successfully</info>');
        }
    }
}

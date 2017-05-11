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

namespace CoreShop\Bundle\CoreBundle\Installer\Checker;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final class CommandDirectoryChecker
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function ensureDirectoryExists($directory, OutputInterface $output)
    {
        if (is_dir($directory)) {
            return;
        }

        try {
            $this->filesystem->mkdir($directory, 0755);

            $output->writeln(sprintf('<comment>Created "%s" directory.</comment>', realpath($directory)));
        } catch (IOException $exception) {
            $output->writeln('');
            $output->writeln('<error>Cannot run command due to unexisting directory (tried to create it automatically, failed).</error>');
            $output->writeln('');

            throw new \RuntimeException(sprintf(
                'Create directory "%s" and run command "<comment>%s</comment>"',
                realpath($directory),
                $this->name
            ));
        }
    }

    public function ensureDirectoryIsWritable($directory, OutputInterface $output)
    {
        if (is_writable($directory)) {
            return;
        }

        try {
            $this->filesystem->chmod($directory, 0755);

            $output->writeln(sprintf('<comment>Changed "%s" permissions to 0755.</comment>', realpath($directory)));
        } catch (IOException $exception) {
            $output->writeln('');
            $output->writeln('<error>Cannot run command due to bad directory permissions (tried to change permissions to 0755).</error>');
            $output->writeln('');

            throw new \RuntimeException(sprintf(
                'Set "%s" writable and run command "<comment>%s</comment>"',
                realpath(dirname($directory)),
                $this->name
            ));
        }
    }

    public function setCommandName($name)
    {
        $this->name = $name;
    }
}

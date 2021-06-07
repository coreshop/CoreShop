<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\IndexBundle;

use Pimcore\Console\Application;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Installer implements InstallerInterface
{
    protected KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function install()
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $options = ['command' => 'coreshop:resources:install'];
        $options = array_merge(
            $options,
            ['--no-interaction' => true, '--application-name object_index']
        );
        $application->run(new ArrayInput($options));

        $options = ['command' => 'coreshop:resources:create-tables'];
        $options = array_merge(
            $options,
            ['application-name' => 'coreshop', '--no-interaction' => true, '--force' => true]
        );
        $application->run(new ArrayInput($options));
    }


    public function uninstall()
    {
        return false;
    }

    public function isInstalled()
    {
        return false;
    }

    public function canBeInstalled()
    {
        return true;
    }

    public function canBeUninstalled()
    {
        return false;
    }

    public function needsReloadAfterInstall()
    {
        return true;
    }

    public function getOutput(): OutputInterface
    {
        return new NullOutput();
    }
}

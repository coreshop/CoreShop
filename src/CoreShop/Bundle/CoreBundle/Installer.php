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

namespace CoreShop\Bundle\CoreBundle;

use Pimcore\Console\Application;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Installer implements InstallerInterface
{
    public function __construct(protected KernelInterface $kernel)
    {
    }

    public function install(): void
    {
        /** @psalm-suppress InternalClass, InternalMethod */
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $options = ['command' => 'coreshop:install'];
        $options = array_merge($options, ['--no-interaction' => true, '--application-name coreshop']);
        $application->run(new ArrayInput($options));
    }

    public function uninstall(): bool
    {
        return false;
    }

    public function isInstalled(): bool
    {
        return false;
    }

    public function canBeInstalled(): bool
    {
        return true;
    }

    public function canBeUninstalled(): bool
    {
        return false;
    }

    public function needsReloadAfterInstall(): bool
    {
        return true;
    }

    public function getOutput(): OutputInterface
    {
        return new NullOutput();
    }

}

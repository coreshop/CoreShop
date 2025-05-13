<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle;

use Pimcore\Console\Application;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class Installer extends SettingsStoreAwareInstaller
{
    public function __construct(
        protected KernelInterface $kernel,
    ) {
        parent::__construct($this->kernel->getBundle('CoreShopIndexBundle'));
    }

    public function install(): void
    {
        /** @psalm-suppress InternalClass, InternalMethod */
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $options = ['command' => 'coreshop:resources:install'];
        $options = array_merge(
            $options,
            ['--no-interaction' => true, '--application-name object_index'],
        );
        $application->run(new ArrayInput($options));

        $options = ['command' => 'coreshop:resources:create-tables'];
        $options = array_merge(
            $options,
            ['application-name' => 'coreshop', '--no-interaction' => true, '--force' => true],
        );
        $application->run(new ArrayInput($options));

        parent::install();
    }

    public function markAllMigrationsInstalled(): void
    {
        $this->markInstalled();
    }

    public function needsReloadAfterInstall(): bool
    {
        return true;
    }

    public function getOutput(): BufferedOutput | NullOutput
    {
        return new NullOutput();
    }
}

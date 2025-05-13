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

namespace CoreShop\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\CoreBundle\Command\InstallDemoCommand;
use CoreShop\Bundle\CoreBundle\Command\InstallFixturesCommand;
use CoreShop\Bundle\CoreBundle\Installer\Checker\CommandDirectoryChecker;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class InstallerContext implements Context
{
    private ?Application $application = null;

    private ?CommandTester $tester = null;

    private ?Command $command = null;

    public function __construct(
        private KernelInterface $kernel,
    ) {
    }

    /**
     * @Given I run CoreShop Install Fixtures Data command
     */
    public function iRunCoreShopInstallFixturesCommand(): void
    {
        $installCommand = new InstallFixturesCommand(
            $this->kernel,
            $this->kernel->getContainer()->get(CommandDirectoryChecker::class),
        );

        $this->application = new Application($this->kernel);
        $this->application->add($installCommand);
        $command = $this->application->find('coreshop:install:fixtures');

        Assert::isInstanceOf($command, InstallFixturesCommand::class);

        $this->command = $command;
        $this->tester = new CommandTester($this->command);
    }

    /**
     * @Given I run CoreShop Install Demo Data command
     */
    public function iRunCoreShopInstallSampleDataCommand(): void
    {
        $installCommand = new InstallDemoCommand(
            $this->kernel,
            $this->kernel->getContainer()->get(CommandDirectoryChecker::class),
        );

        $this->application = new Application($this->kernel);
        $this->application->add($installCommand);
        $command = $this->application->find('coreshop:install:demo');

        Assert::isInstanceOf($command, InstallDemoCommand::class);

        $this->command = $command;
        $this->tester = new CommandTester($this->command);
    }

    /**
     * @Given I confirm loading Fixtures Data command
     */
    public function iConfirmLoadingFixtures(): void
    {
        $this->iExecuteCommandAndConfirm('coreshop:install:fixtures');
    }

    /**
     * @Given I confirm loading Demo Data command
     */
    public function iConfirmLoadingDemo(): void
    {
        $this->iExecuteCommandAndConfirm('coreshop:install:demo');
    }

    /**
     * @Then the command should finish successfully
     */
    public function commandSuccess(): void
    {
        Assert::same($this->tester->getStatusCode(), 0);
    }

    /**
     * @param string $name
     */
    private function iExecuteCommandAndConfirm($name): void
    {
        $this->tester->setInputs(['y']);
        $this->tester->execute(['command' => $name]);
    }
}

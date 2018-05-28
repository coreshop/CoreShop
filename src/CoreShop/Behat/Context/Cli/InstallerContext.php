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

namespace CoreShop\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\CoreBundle\Command\InstallCommand;
use CoreShop\Bundle\CoreBundle\Command\InstallDemoCommand;
use CoreShop\Bundle\CoreBundle\Command\InstallFixturesCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class InstallerContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var CommandTester
     */
    private $tester;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var InstallCommand
     */
    private $command;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given I run CoreShop Install Fixtures Data command
     */
    public function iRunCoreShopInstallFixturesCommand()
    {
        $installCommand = new InstallFixturesCommand(
            $this->kernel,
            $this->kernel->getContainer()->get('coreshop.installer.checker.command_directory')
        );

        $this->application = new Application($this->kernel);
        $this->application->add($installCommand);
        $this->command = $this->application->find('coreshop:install:fixtures');
        $this->tester = new CommandTester($this->command);
    }

    /**
     * @Given I run CoreShop Install Demo Data command
     */
    public function iRunCoreShopInstallSampleDataCommand()
    {
        $installCommand = new InstallDemoCommand(
            $this->kernel,
            $this->kernel->getContainer()->get('coreshop.installer.checker.command_directory')
        );

        $this->application = new Application($this->kernel);
        $this->application->add($installCommand);
        $this->command = $this->application->find('coreshop:install:demo');
        $this->tester = new CommandTester($this->command);
    }

    /**
     * @Given I confirm loading Fixtures Data command
     */
    public function iConfirmLoadingFixtures()
    {
        $this->iExecuteCommandAndConfirm('coreshop:install:fixtures');
    }

    /**
     * @Given I confirm loading Demo Data command
     */
    public function iConfirmLoadingDemo()
    {
        $this->iExecuteCommandAndConfirm('coreshop:install:demo');
    }

    /**
     * @Then the command should finish successfully
     */
    public function commandSuccess()
    {
        Assert::same($this->tester->getStatusCode(), 0);
    }

    /**
     * @param string $input
     *
     * @return resource
     */
    private function getInputStream($input)
    {
        $stream = fopen('php://memory', 'rb+', false);
        fwrite($stream, $input);
        rewind($stream);

        return $stream;
    }

    /**
     * @param string $name
     */
    private function iExecuteCommandAndConfirm($name)
    {
        $this->questionHelper = $this->command->getHelper('question');
        $inputString = 'y' . PHP_EOL;
        $this->questionHelper->setInputStream($this->getInputStream($inputString));

        try {
            $this->tester->execute(['command' => $name]);
        } catch (\Exception $e) {
        }
    }
}

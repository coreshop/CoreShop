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

use CoreShop\Bundle\ResourceBundle\Installer\Configuration\GridConfigConfiguration;
use CoreShop\Component\Pimcore\GridConfigInstallerInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

final class PimcoreGridConfigInstaller implements ResourceInstallerInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var GridConfigInstallerInterface
     */
    protected $gridConfigInstaller;

    /**
     * @param KernelInterface $kernel
     * @param GridConfigInstallerInterface $gridConfigInstaller
     */
    public function __construct(
        KernelInterface $kernel,
        GridConfigInstallerInterface $gridConfigInstaller
    )
    {
        $this->kernel = $kernel;
        $this->gridConfigInstaller = $gridConfigInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output, $applicationName = null)
    {
        $parameter = $applicationName ? sprintf('%s.pimcore.admin.install.grid_config', $applicationName) : 'coreshop.all.pimcore.admin.install.grid_config';

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $routeFilesToInstall = $this->kernel->getContainer()->getParameter($parameter);
            $gridConfigsToInstall = [];

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            $processor = new Processor();
            $configurationDefinition = new GridConfigConfiguration();

            foreach ($routeFilesToInstall as $file) {
                $file = $this->kernel->locateResource($file);

                if (file_exists($file)) {
                    $gridConfigs = Yaml::parse(file_get_contents($file));
                    $gridConfigs = $processor->processConfiguration($configurationDefinition, ['grid_config' => $gridConfigs]);
                    $gridConfigs = $gridConfigs['grid_config'];

                    foreach ($gridConfigs as $name => $gridConfigData) {
                        $gridConfigsToInstall[$name] = $gridConfigData;
                    }
                }
            }

            $progress->start(count($gridConfigsToInstall));

            foreach ($gridConfigsToInstall as $name => $gridData) {
                $class = ClassDefinition::getByName($gridData['class']);

                if (!$class instanceof ClassDefinition) {
                    throw new \InvalidArgumentException(sprintf('Class with name "%s" not found', $gridData['class']));
                }

                $progress->setMessage(sprintf('<error>Install Grid Config %s</error>', $name));

                $this->gridConfigInstaller->installGridConfig($gridData['data'], $gridData['name'], $class->getId(), true);

                $progress->advance();
            }

            $progress->finish();
        }
    }
}
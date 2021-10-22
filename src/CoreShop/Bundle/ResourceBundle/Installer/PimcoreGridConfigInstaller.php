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

use CoreShop\Bundle\ResourceBundle\Installer\Configuration\GridConfigConfiguration;
use CoreShop\Bundle\ResourceBundle\Pimcore\ObjectManager;
use CoreShop\Component\Pimcore\DataObject\GridConfigInstallerInterface;
use CoreShop\Component\Resource\Metadata\RegistryInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

final class PimcoreGridConfigInstaller implements ResourceInstallerInterface
{
    public function __construct(private KernelInterface $kernel, private RegistryInterface $metaDataRegistry, private ObjectManager $objectManager, private GridConfigInstallerInterface $gridConfigInstaller, private PimcoreClassInstallerInterface $pimcoreClassInstaller)
    {
    }

    public function installResources(OutputInterface $output, string $applicationName = null, array $options = []): void
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
                $progress->setMessage(sprintf('Install Grid Config %s', $name));

                $this->gridConfigInstaller->installGridConfig($gridData['data'], $gridData['name'], $this->findClassId($gridData['class']), true);

                $progress->advance();
            }

            $progress->finish();
            $progress->clear();

            $output->writeln('  - <info>Grid Configs have been installed successfully</info>');
        }
    }

    private function findClassId(string $classIdentifier): string
    {
        $metadata = $this->metaDataRegistry->get($classIdentifier);

        try {
            $repository = $this->objectManager->getRepository($metadata->getParameter('model'));

            return $repository->getClassId();
        } catch (\InvalidArgumentException) {
        }

        $freshlyInstalledClasses = $this->pimcoreClassInstaller->getInstalledClasses();

        if (isset($freshlyInstalledClasses[$classIdentifier])) {
            $class = $freshlyInstalledClasses[$classIdentifier];

            if ($class instanceof ClassDefinition) {
                return $class->getId();
            }
        }

        throw new \InvalidArgumentException(sprintf('Could\'nt find ClassID for Identifier %s', $classIdentifier));
    }
}

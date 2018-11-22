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
use CoreShop\Bundle\ResourceBundle\Pimcore\ObjectManager;
use CoreShop\Component\Pimcore\DataObject\GridConfigInstallerInterface;
use CoreShop\Component\Resource\Metadata\RegistryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

final class PimcoreGridConfigInstaller implements ResourceInstallerInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var RegistryInterface
     */
    protected $metaDataRegistry;

    /**
     * @var GridConfigInstallerInterface
     */
    protected $gridConfigInstaller;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var PimcoreClassInstallerInterface
     */
    protected $pimcoreClassInstaller;

    /**
     * @param KernelInterface $kernel
     * @param RegistryInterface $metaDataRegistry
     * @param ObjectManager $objectManager
     * @param GridConfigInstallerInterface $gridConfigInstaller
     * @param PimcoreClassInstallerInterface $classInstaller
     */
    public function __construct(
        KernelInterface $kernel,
        RegistryInterface $metaDataRegistry,
        ObjectManager $objectManager,
        GridConfigInstallerInterface $gridConfigInstaller,
        PimcoreClassInstallerInterface $classInstaller
    )
    {
        $this->kernel = $kernel;
        $this->metaDataRegistry = $metaDataRegistry;
        $this->objectManager = $objectManager;
        $this->gridConfigInstaller = $gridConfigInstaller;
        $this->pimcoreClassInstaller = $classInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output, $applicationName = null, $options = [])
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
                $progress->setMessage(sprintf('<error>Install Grid Config %s</error>', $name));

                $this->gridConfigInstaller->installGridConfig($gridData['data'], $gridData['name'], $this->findClassId($gridData['class']), true);

                $progress->advance();
            }

            $progress->finish();
        }
    }

    /**
     * @param string $classIdentifier
     * @return int
     */
    protected function findClassId($classIdentifier)
    {
        $metadata = $this->metaDataRegistry->get($classIdentifier);

        try {
            $repository = $this->objectManager->getRepository($metadata->getParameter('model'));

            if ($repository instanceof PimcoreRepositoryInterface) {
                return $repository->getClassId();
            }
        }
        catch (\InvalidArgumentException $ex) {

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
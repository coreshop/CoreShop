<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ResourceBundle\Installer;

use Pimcore\Extension\Bundle\Exception\BundleNotFoundException;
use Pimcore\Extension\Bundle\PimcoreBundleManager;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class PimcoreDependantBundleInstaller implements ResourceInstallerInterface
{
    private KernelInterface $kernel;
    private PimcoreBundleManager $bundleManager;

    public function __construct(KernelInterface $kernel, PimcoreBundleManager $bundleManager)
    {
        $this->kernel = $kernel;
        $this->bundleManager = $bundleManager;
    }

    public function installResources(OutputInterface $output, string $applicationName = null, array $options = []): void
    {
        $parameter = $applicationName ?
            sprintf('%s.dependant.bundles', $applicationName) :
            'coreshop.all.dependant.bundles'
        ;

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $bundlesToInstall = $this->kernel->getContainer()->getParameter($parameter);

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            $progress->start(count($bundlesToInstall));

            foreach ($bundlesToInstall as $bundleName) {
                $progress->setMessage(sprintf('Install Bundle "%s"', $bundleName));

                try {
                    $bundle = $this->bundleManager->getActiveBundle($bundleName, false);

                    if ($this->bundleManager->canBeInstalled($bundle)) {
                        $this->bundleManager->install($bundle);
                    }
                } catch (BundleNotFoundException $ex) {
                    $progress->setMessage(sprintf('<error>Bundle not found "%s"</error>', $bundleName));
                }

                $progress->advance();
            }

            $progress->finish();
            $progress->clear();

            $output->writeln('  - <info>Dependant Bundles have been installed successfully</info>');
        }
    }
}

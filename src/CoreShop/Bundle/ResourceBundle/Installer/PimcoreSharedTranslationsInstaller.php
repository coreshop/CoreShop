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

use CoreShop\Bundle\ResourceBundle\Installer\Configuration\SharedTranslationConfiguration;
use Pimcore\Model\Translation\Website;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

final class PimcoreSharedTranslationsInstaller implements ResourceInstallerInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**<
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output, $applicationName = null)
    {
        $parameter = $applicationName ? sprintf('%s.application.pimcore.admin.install.translations', $applicationName) : 'resources.admin.install.translations';

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $translationFilesToInstall = $this->kernel->getContainer()->getParameter($parameter);
            $translationsToInstall = [];

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            $processor = new Processor();
            $configurationDefinition = new SharedTranslationConfiguration();

            foreach ($translationFilesToInstall as $file) {
                $file = $this->kernel->locateResource($file);

                if (file_exists($file)) {
                    $translations = Yaml::parse(file_get_contents($file));
                    $translations = $processor->processConfiguration($configurationDefinition, ['translations' => $translations]);
                    $translations = $translations['translations'];

                    foreach ($translations as $name => $translationData) {
                        $translationsToInstall[$name] = $translationData;
                    }
                }
            }

            $progress->start(count($translationsToInstall));

            foreach ($translationsToInstall as $name => $translationData) {
                $progress->setMessage(sprintf('<error>Install Translation %s</error>', $name));

                $this->installTranslation($name, $translationData);

                $progress->advance();
            }

            $progress->finish();
        }
    }

    /**
     * Check if route is already installed
     *
     * @param $name
     * @param $properties
     * @return Website
     */
    private function installTranslation($name, $properties)
    {
        $translation = Website::getByKey($name, true);
        $translation->setTranslations($properties['languages']);
        $translation->save();

        return $translation;
    }
}
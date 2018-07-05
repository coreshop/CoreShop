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

use CoreShop\Bundle\ResourceBundle\Installer\Configuration\ImageThumbnailConfiguration;
use Pimcore\Model\Asset\Image\Thumbnail\Config;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

final class PimcoreImageThumbnailsInstaller implements ResourceInstallerInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

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
    public function installResources(OutputInterface $output, $applicationName = null, $options = [])
    {
        $parameter = $applicationName ? sprintf('%s.pimcore.admin.install.image_thumbnails', $applicationName) : 'coreshop.all.pimcore.admin.install.image_thumbnails';

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $thumbnailFilesToInstall = $this->kernel->getContainer()->getParameter($parameter);
            $thumbnailsToInstall = [];

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            $processor = new Processor();
            $configurationDefinition = new ImageThumbnailConfiguration();

            foreach ($thumbnailFilesToInstall as $file) {
                $file = $this->kernel->locateResource($file);

                if (file_exists($file)) {
                    $thumbnails = Yaml::parse(file_get_contents($file));
                    $thumbnails = $processor->processConfiguration($configurationDefinition, ['thumbnails' => $thumbnails]);
                    $thumbnails = $thumbnails['thumbnails'];

                    foreach ($thumbnails as $name => $thumbnailData) {
                        $thumbnailsToInstall[$name] = $thumbnailData;
                    }
                }
            }

            $progress->start(count($thumbnailsToInstall));

            foreach ($thumbnailsToInstall as $name => $thumbnailData) {
                $progress->setMessage(sprintf('<error>Install Image Thumbnail %s</error>', $name));

                $this->installThumbnail($name, $thumbnailData);

                $progress->advance();
            }

            $progress->finish();
        }
    }

    /**
     * Check if Image Thumbnail is already installed.
     *
     * @param $name
     * @param $properties
     *
     * @return Config
     */
    private function installThumbnail($name, $properties)
    {
        $thumbnailConfig = new Config();

        try {
            $thumbnailConfig->getDao()->getByName($name, null);
        } catch (\Exception $e) {
            //Thumbnail does not exist, so we install it
            $thumbnailConfig = new Config();
            $thumbnailConfig->setName($name);
            $thumbnailConfig->setItems($properties['items']);
            $thumbnailConfig->setDescription($properties['description']);
            $thumbnailConfig->setFormat($properties['format']);
            $thumbnailConfig->setQuality($properties['quality']);
            $thumbnailConfig->setHighResolution($properties['highResolution']);
            $thumbnailConfig->setPreserveColor($properties['preserveColor']);
            $thumbnailConfig->setPreserveMetaData($properties['preserveMetaData']);
            $thumbnailConfig->save();
        }

        return $thumbnailConfig;
    }
}

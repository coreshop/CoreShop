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

use CoreShop\Bundle\ResourceBundle\Installer\Configuration\ImageThumbnailConfiguration;
use Pimcore\Model\Asset\Image\Thumbnail\Config;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

final class PimcoreImageThumbnailsInstaller implements ResourceInstallerInterface
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function installResources(OutputInterface $output, string $applicationName = null, array $options = []): void
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
                $progress->setMessage(sprintf('Install Image Thumbnail %s', $name));

                $this->installThumbnail($name, $thumbnailData);

                $progress->advance();
            }

            $progress->finish();
            $progress->clear();

            $output->writeln('  - <info>Image Thumbnails have been installed successfully</info>');
        }
    }

    private function installThumbnail(string $name, array $properties): Config
    {
        $thumbnailConfig = new Config();

        try {
            /**
             * @var \Pimcore\Model\Asset\Image\Thumbnail\Config\Dao
             */
            $dao = $thumbnailConfig->getDao();
            /**
             * @psalm-suppress InternalMethod
             */
            $dao->getByName($name);
        } catch (\Exception $e) {
            //Thumbnail does not exist, so we install it
            $thumbnailConfig = new Config();
            $thumbnailConfig->setName($name);
            $thumbnailConfig->setItems($properties['items']);
            $thumbnailConfig->setDescription($properties['description'] ?? '');
            $thumbnailConfig->setGroup($properties['group']);
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

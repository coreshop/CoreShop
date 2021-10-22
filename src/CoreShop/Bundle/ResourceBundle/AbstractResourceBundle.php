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

namespace CoreShop\Bundle\ResourceBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Driver\Exception\UnknownDriverException;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use PackageVersions\Versions;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

abstract class AbstractResourceBundle extends Bundle implements ResourceBundleInterface, DependentBundleInterface
{
    protected string $mappingFormat = ResourceBundleInterface::MAPPING_XML;

    public function build(ContainerBuilder $container): void
    {
        if (null !== $this->getModelNamespace()) {
            foreach ($this->getSupportedDrivers() as $driver) {
                list($compilerPassClassName, $compilerPassMethod) = $this->getMappingCompilerPassInfo($driver);

                if (class_exists($compilerPassClassName)) {
                    if (!method_exists($compilerPassClassName, $compilerPassMethod)) {
                        throw new InvalidConfigurationException(
                            "The 'mappingFormat' value is invalid, must be 'xml', 'yml' or 'annotation'."
                        );
                    }

                    switch ($this->mappingFormat) {
                        case ResourceBundleInterface::MAPPING_XML:
                        case ResourceBundleInterface::MAPPING_YAML:
                            $container->addCompilerPass($compilerPassClassName::$compilerPassMethod(
                                [$this->getConfigFilesPath() => $this->getModelNamespace()],
                                [$this->getObjectManagerParameter()],
                                sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                            ));

                            break;

                        case ResourceBundleInterface::MAPPING_ANNOTATION:
                            $container->addCompilerPass($compilerPassClassName::$compilerPassMethod(
                                [$this->getModelNamespace()],
                                [$this->getConfigFilesPath()],
                                [sprintf('%s.object_manager', $this->getBundlePrefix())],
                                sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                            ));

                            break;
                    }
                }
            }
        }
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $collection->addBundle(new CoreShopResourceBundle(), 3800);
    }

    public function getVersion(): string
    {
        if (class_exists('\\CoreShop\\Bundle\\CoreBundle\\Application\\Version')) {
            return \CoreShop\Bundle\CoreBundle\Application\Version::getVersion().' ('.$this->getComposerVersion().')';
        }

        return $this->getComposerVersion();
    }

    public function getComposerVersion(): string
    {
        if ($this instanceof ComposerPackageBundleInterface) {
            $bundleName = $this->getPackageName();

            if (class_exists(InstalledVersions::class)) {
                if (InstalledVersions::isInstalled('coreshop/core-shop')) {
                    return InstalledVersions::getVersion('coreshop/core-shop');
                }

                if (InstalledVersions::isInstalled($bundleName)) {
                    return InstalledVersions::getVersion($bundleName);
                }
            }
        }

        if (class_exists(Version::class)) {
            return Version::getVersion();
        }

        return '';
    }

    protected function getBundlePrefix(): string
    {
        return Container::underscore(substr(strrchr($this::class, '\\'), 1, -6));
    }

    protected function getDoctrineMappingDirectory(): string
    {
        return 'model';
    }

    protected function getModelNamespace(): ?string
    {
        return null;
    }

    protected function getMappingCompilerPassInfo(string $driverType): array
    {
        $mappingsPassClassname = match ($driverType) {
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM => DoctrineOrmMappingsPass::class,
            default => throw new UnknownDriverException($driverType),
        };

        $compilerPassMethod = sprintf('create%sMappingDriver', ucfirst($this->mappingFormat));

        return [$mappingsPassClassname, $compilerPassMethod];
    }

    protected function getConfigFilesPath(): string
    {
        return sprintf(
            '%s/Resources/config/doctrine/%s',
            $this->getPath(),
            strtolower($this->getDoctrineMappingDirectory())
        );
    }

    protected function getObjectManagerParameter(): string
    {
        return sprintf('%s.object_manager', $this->getBundlePrefix());
    }
}

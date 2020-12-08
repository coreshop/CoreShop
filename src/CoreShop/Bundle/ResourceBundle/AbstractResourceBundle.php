<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
    /**
     * Configure format of mapping files.
     *
     * @var string
     */
    protected $mappingFormat = ResourceBundleInterface::MAPPING_YAML;

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
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

    /**
     * {@inheritdoc}
     */
    public static function registerDependentBundles(BundleCollection $collection)
    {
        $collection->addBundle(new CoreShopResourceBundle(), 3800);
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        if (class_exists('\\CoreShop\\Bundle\\CoreBundle\\Application\\Version')) {
            return \CoreShop\Bundle\CoreBundle\Application\Version::getVersion().' ('.$this->getComposerVersion().')';
        }

        return $this->getComposerVersion();
    }

    /**
     * @return string
     */
    public function getComposerVersion()
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

            if (class_exists(Versions::class)) {
                if (isset(Versions::VERSIONS[$bundleName])) {
                    return Versions::getVersion($bundleName);
                }

                if (isset(Versions::VERSIONS['coreshop/core-shop'])) {
                    return Versions::getVersion('coreshop/core-shop');
                }
            }
        }

        if (class_exists(Version::class)) {
            return Version::getVersion();
        }

        return '';
    }

    /**
     * Return the prefix of the bundle.
     *
     * @return string
     */
    protected function getBundlePrefix()
    {
        return Container::underscore(substr(strrchr(get_class($this), '\\'), 1, -6));
    }

    /**
     * Return the directory where are stored the doctrine mapping.
     *
     * @return string
     */
    protected function getDoctrineMappingDirectory()
    {
        return 'model';
    }

    /**
     * Return the entity namespace.
     *
     * @return string|null
     */
    protected function getModelNamespace()
    {
        return null;
    }

    /**
     * Return mapping compiler pass class depending on driver.
     *
     * @param string $driverType
     *
     * @return array
     *
     * @throws UnknownDriverException
     */
    protected function getMappingCompilerPassInfo($driverType)
    {
        switch ($driverType) {
            case CoreShopResourceBundle::DRIVER_DOCTRINE_ORM:
                $mappingsPassClassname = DoctrineOrmMappingsPass::class;

                break;
            default:
                throw new UnknownDriverException($driverType);
        }

        $compilerPassMethod = sprintf('create%sMappingDriver', ucfirst($this->mappingFormat));

        return [$mappingsPassClassname, $compilerPassMethod];
    }

    /**
     * Return the absolute path where are stored the doctrine mapping.
     *
     * @return string
     */
    protected function getConfigFilesPath()
    {
        return sprintf(
            '%s/Resources/config/doctrine/%s',
            $this->getPath(),
            strtolower($this->getDoctrineMappingDirectory())
        );
    }

    /**
     * @return string
     */
    protected function getObjectManagerParameter()
    {
        return sprintf('%s.object_manager', $this->getBundlePrefix());
    }
}

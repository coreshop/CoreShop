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

namespace CoreShop\Bundle\PimcoreBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\ExpressionLanguageServicePass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterGridActionPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterGridFilterPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterPimcoreDocumentTagImplementationLoaderPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterPimcoreDocumentTagPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterTypeHintRegistriesPass;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopPimcoreBundle extends AbstractPimcoreBundle
{
    public function getNiceName(): string
    {
        return 'CoreShop - Pimcore';
    }

    public function getDescription(): string
    {
        return 'CoreShop - Pimcore Bundle';
    }

    public function getVersion(): string
    {
        $bundleName = 'coreshop/pimcore-bundle';

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

        if (class_exists(Version::class)) {
            return Version::getVersion();
        }

        return '';
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterGridActionPass());
        $container->addCompilerPass(new RegisterGridFilterPass());
        $container->addCompilerPass(new RegisterPimcoreDocumentTagImplementationLoaderPass());
        $container->addCompilerPass(new RegisterPimcoreDocumentTagPass());
        $container->addCompilerPass(new ExpressionLanguageServicePass());
        $container->addCompilerPass(new RegisterTypeHintRegistriesPass());
    }
}

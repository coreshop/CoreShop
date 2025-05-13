<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PimcoreBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\ExpressionLanguageServicePass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\LinkGeneratorPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterGridActionPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterGridFilterPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterPimcoreDocumentTagImplementationLoaderPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterPimcoreDocumentTagPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterTypeHintRegistriesPass;
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
        if (class_exists('\\CoreShop\\Bundle\\CoreBundle\\Application\\Version')) {
            return \CoreShop\Bundle\CoreBundle\Application\Version::getVersion() . ' (' . $this->getComposerVersion() . ')';
        }

        return $this->getComposerVersion();
    }

    public function getComposerVersion(): string
    {
        $bundleName = 'coreshop/pimcore-bundle';

        if (class_exists(InstalledVersions::class)) {
            if (InstalledVersions::isInstalled('coreshop/core-shop')) {
                return InstalledVersions::getPrettyVersion('coreshop/core-shop');
            }

            if (InstalledVersions::isInstalled($bundleName)) {
                return InstalledVersions::getPrettyVersion($bundleName);
            }
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
        $container->addCompilerPass(new LinkGeneratorPass());
    }
}

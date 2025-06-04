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

namespace CoreShop\Bundle\SEOBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\SEOBundle\DependencyInjection\Compiler\ExtractorRegistryServicePass;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopSEOBundle extends AbstractPimcoreBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ExtractorRegistryServicePass());
    }

    public function getNiceName(): string
    {
        return 'CoreShop - SEO';
    }

    public function getDescription(): string
    {
        return 'CoreShop - SEO Bundle';
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
        $bundleName = 'coreshop/seo-bundle';

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
}

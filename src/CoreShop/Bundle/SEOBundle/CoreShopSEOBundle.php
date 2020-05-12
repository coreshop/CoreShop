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

namespace CoreShop\Bundle\SEOBundle;

use CoreShop\Bundle\SEOBundle\DependencyInjection\Compiler\ExtractorRegistryServicePass;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @deprecated Deprecated due to https://github.com/dachcom-digital/pimcore-seo, will be removed with 3.1
 */
final class CoreShopSEOBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        @trigger_error(
            'CoreShopSeoBundle is deprecated due to https://github.com/dachcom-digital/pimcore-seo, will be removed with 3.1',
            E_USER_DEPRECATED
        );

        $container->addCompilerPass(new ExtractorRegistryServicePass());
    }

    /**
     * {@inheritdoc}
     */
    public function getNiceName(): string
    {
        return 'CoreShop - SEO';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return 'CoreShop - SEO Bundle';
    }

    /**
     * @return string
     */
    public function getComposerPackageName(): string
    {
        if (isset(Versions::VERSIONS['coreshop/seo-bundle'])) {
            return 'coreshop/seo-bundle';
        }

        return 'coreshop/core-shop';
    }
}

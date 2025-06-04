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

namespace CoreShop\Bundle\TrackingBundle;

use CoreShop\Bundle\TrackingBundle\DependencyInjection\Compiler\TrackerPass;
use CoreShop\Bundle\TrackingBundle\DependencyInjection\Compiler\TrackingExtractorPass;
use Pimcore\Bundle\GoogleMarketingBundle\PimcoreGoogleMarketingBundle;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CoreShopTrackingBundle extends Bundle implements DependentBundleInterface
{
    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $collection->addBundle(new PimcoreGoogleMarketingBundle(), 1000);
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TrackerPass());
        $container->addCompilerPass(new TrackingExtractorPass());
    }
}

<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\TrackingBundle;

use CoreShop\Bundle\TrackingBundle\DependencyInjection\Compiler\TrackerPass;
use CoreShop\Bundle\TrackingBundle\DependencyInjection\Compiler\TrackingExtractorPass;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

final class CoreShopTrackingBundle extends Bundle implements DependentBundleInterface
{
    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $googleMarketingBundleClass = self::getGoogleMarketingBundleClass();
        if (null !== $googleMarketingBundleClass) {
            $collection->addBundle(new $googleMarketingBundleClass(), 1000);
        }
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TrackerPass());
        $container->addCompilerPass(new TrackingExtractorPass());
    }

    /**
     * @return class-string<BundleInterface>|null
     */
    private static function getGoogleMarketingBundleClass(): ?string
    {
        $googleMarketingBundleClass = sprintf('Pimcore\\Bundle\\%s\\PimcoreGoogleMarketingBundle', 'GoogleMarketingBundle');

        if (!class_exists($googleMarketingBundleClass) || !is_subclass_of($googleMarketingBundleClass, BundleInterface::class)) {
            return null;
        }

        return $googleMarketingBundleClass;
    }
}

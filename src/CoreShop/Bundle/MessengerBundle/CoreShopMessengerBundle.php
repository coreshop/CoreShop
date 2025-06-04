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

namespace CoreShop\Bundle\MessengerBundle;

use CoreShop\Bundle\MenuBundle\CoreShopMenuBundle;
use CoreShop\Bundle\MessengerBundle\DependencyInjection\CompilerPass\FailureReceiverPass;
use CoreShop\Bundle\MessengerBundle\DependencyInjection\CompilerPass\ReceiverPass;
use CoreShop\Bundle\PimcoreBundle\CoreShopPimcoreBundle;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CoreShopMessengerBundle extends Bundle implements DependentBundleInterface
{
    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $collection->addBundle(new CoreShopPimcoreBundle(), 3850);
        $collection->addBundle(new CoreShopMenuBundle(), 4000);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new ReceiverPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -100);
        $container->addCompilerPass(new FailureReceiverPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -100);
    }
}

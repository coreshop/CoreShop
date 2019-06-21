<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use Pimcore\Kernel;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundlesToCollection(\Pimcore\HttpKernel\BundleCollection\BundleCollection $collection)
    {
        $collection->addBundle(new \CoreShop\Bundle\CoreBundle\CoreShopCoreBundle());
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        \Pimcore::setKernel($this);
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(\Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(function (\Symfony\Component\DependencyInjection\ContainerBuilder $container) use ($loader) {
            $container->addCompilerPass(new \CoreShop\Test\DependencyInjection\MakeServicesPublicPass(), \Symfony\Component\DependencyInjection\Compiler\PassConfig::TYPE_BEFORE_OPTIMIZATION, -100000);
            $container->addCompilerPass(new \CoreShop\Test\DependencyInjection\MakePimcoreServicesPublicPass(), \Symfony\Component\DependencyInjection\Compiler\PassConfig::TYPE_BEFORE_OPTIMIZATION, -100000);
            $container->addCompilerPass(new \CoreShop\Test\DependencyInjection\MonologChannelLoggerPass(), \Symfony\Component\DependencyInjection\Compiler\PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        });
    }
}

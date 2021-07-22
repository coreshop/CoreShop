<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use Pimcore\Kernel as PimcoreKernel;

class BehatKernel extends PimcoreKernel
{
    public function registerBundlesToCollection(\Pimcore\HttpKernel\BundleCollection\BundleCollection $collection)
    {
        $collection->addBundle(new \CoreShop\Bundle\CoreBundle\CoreShopCoreBundle());
        $collection->addBundle(new \FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle());
        $collection->addBundle(new \CoreShop\Bundle\TestBundle\CoreShopTestBundle(), 0);
    }

    public function boot()
    {
        parent::boot();

        \Pimcore::setKernel($this);
    }

    protected function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        parent::build($container);

        $container->setParameter('pimcore.geoip.db_file', $container->getParameter('kernel.project_dir') . '/var/config/GeoLite2-City.mmdb');
    }
}

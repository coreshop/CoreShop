<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use Pimcore\Kernel;

class TestAppKernel extends Kernel
{
    /**
     * AppKernel constructor.
     */
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);
    }

    public function registerBundlesToCollection(\Pimcore\HttpKernel\BundleCollection\BundleCollection $collection)
    {
        \CoreShop\Bundle\CoreBundle\Application\RegisterBundleHelper::registerBundles($collection);

        $collection->addBundle(new \CoreShop\Bundle\AdminBundle\CoreShopAdminBundle());
    }

    public function getProjectDir()
    {
        return '../';
    }
}

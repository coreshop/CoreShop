<?php

declare(strict_types=1);
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

namespace App;

use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Pimcore\Kernel as PimcoreKernel;

class Kernel extends PimcoreKernel
{
    public function registerBundlesToCollection(BundleCollection $collection)
    {
        $collection->addBundle(new \CoreShop\Bundle\CoreBundle\CoreShopCoreBundle());
        $collection->addBundle(new \FriendsOfBehat\SymfonyExtension\Bundle\FriendsOfBehatSymfonyExtensionBundle());
        $collection->addBundle(new \CoreShop\Bundle\TestBundle\CoreShopTestBundle(), 0);
    }

    protected function getEnvironmentsForDevBundles(): array
    {
        return array_merge(
            ['test_precision'],
            parent::getEnvironmentsForDevBundles()
        );
    }

    public function boot()
    {
        \Pimcore::setKernel($this);

        parent::boot();
    }
}

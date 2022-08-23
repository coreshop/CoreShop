<?php
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

declare(strict_types=1);

namespace CoreShop\Component\StorageList\Factory;

use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class StorageListFactory implements FactoryInterface
{
    public function __construct(private FactoryInterface $storageListFactory)
    {
    }

    public function createNew()
    {
        $storageList = $this->storageListFactory->createNew();

        if ($storageList instanceof AbstractPimcoreModel) {
            $storageList->setKey(uniqid('wishlist', true));
            $storageList->setPublished(true);
        }

        return $storageList;
    }
}

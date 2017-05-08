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
 *
*/

namespace CoreShop\Bundle\ProductBundle\Repository;

use CoreShop\Bundle\ResourceBundle\Repository\PimcoreRepository;
use CoreShop\Component\Product\Repository\ProductRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;

class ProductRepository extends PimcoreRepository implements ProductRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLatestByShop(StoreInterface $store, $count = 8)
    {
        $list = $this->getList();
        $list->setCondition('enabled = 1');
        $list->setLimit($count);
        $list->setOrderKey('o_creationDate');
        $list->setOrder('DESC');

        return $list->getObjects();
    }
}
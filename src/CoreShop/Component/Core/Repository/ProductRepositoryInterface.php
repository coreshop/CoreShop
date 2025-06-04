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

namespace CoreShop\Component\Core\Repository;

use CoreShop\Component\Product\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Pimcore\Model\DataObject\Listing;

interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    public function findLatestByStore(StoreInterface $store, int $count = 8): array;

    public function getProducts(array $options = []): array;

    public function getProductsListing(array $options = []): Listing;
}

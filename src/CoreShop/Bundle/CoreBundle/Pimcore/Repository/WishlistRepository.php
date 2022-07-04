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

namespace CoreShop\Bundle\CoreBundle\Pimcore\Repository;

use CoreShop\Component\Core\Wishlist\Repository\WishlistRepositoryInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Store\Model\StoreInterface;
use CoreShop\Component\Wishlist\Model\WishlistInterface;

class WishlistRepository extends \CoreShop\Bundle\WishlistBundle\Pimcore\Repository\WishlistRepository implements WishlistRepositoryInterface
{
    public function findLatestByStoreAndCustomer(
        StoreInterface $store,
        CustomerInterface $customer
    ): ?WishlistInterface {
        $list = $this->getList();
        $list->setCondition('customer__id = ? AND store = ?', [$customer->getId(), $store->getId()]);
        $list->setOrderKey('o_creationDate');
        $list->setOrder('DESC');
        $list->load();

        $objects = $list->getObjects();

        if (count($objects) > 0 && $objects[0] instanceof WishlistInterface) {
            return $objects[0];
        }

        return null;
    }

}

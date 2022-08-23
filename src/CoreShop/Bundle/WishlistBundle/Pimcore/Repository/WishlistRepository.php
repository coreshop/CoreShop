<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\WishlistBundle\Pimcore\Repository;

use CoreShop\Bundle\ResourceBundle\Pimcore\PimcoreRepository;
use CoreShop\Component\Wishlist\Model\WishlistInterface;
use CoreShop\Component\Wishlist\Repository\WishlistRepositoryInterface;

class WishlistRepository extends PimcoreRepository implements WishlistRepositoryInterface
{
    public function findByToken(string $token): ?WishlistInterface
    {
        $list = $this->getList();
        $list->setCondition('token = ?', [$token]);
        $list->load();

        if ($list->getTotalCount() === 1) {
            $objects = $list->getObjects();

            return $objects[0];
        }

        return null;
    }
}

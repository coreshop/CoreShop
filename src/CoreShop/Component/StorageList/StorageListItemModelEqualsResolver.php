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

namespace CoreShop\Component\StorageList;

use CoreShop\Component\StorageList\Model\StorageListItemInterface;

class StorageListItemModelEqualsResolver implements StorageListItemResolverInterface
{
    public function equals(StorageListItemInterface $itemA, StorageListItemInterface $itemB): bool
    {
        return $itemA->equals($itemB);
    }
}

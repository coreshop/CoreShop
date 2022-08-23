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

namespace CoreShop\Component\Order\Context;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\StorageList\Context\CompositeStorageListContext;

final class CompositeCartContext extends CompositeStorageListContext implements CartContextInterface
{
    public function getCart(): OrderInterface
    {
        return $this->getStorageList();
    }

    public function getStorageList(): OrderInterface
    {
        $order = parent::getStorageList();

        if (!$order instanceof OrderInterface) {
            throw new CartNotFoundException();
        }

        return $order;
    }
}

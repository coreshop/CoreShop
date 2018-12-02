<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;

interface OrderDocumentItemTransformerInterface
{
    /**
     * @param OrderDocumentInterface     $orderDocument
     * @param OrderItemInterface         $orderItem
     * @param OrderDocumentItemInterface $documentItem
     * @param int                        $quantity
     *
     * @return OrderDocumentItemInterface
     */
    public function transform(OrderDocumentInterface $orderDocument, OrderItemInterface $orderItem, OrderDocumentItemInterface $documentItem, $quantity);
}

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

namespace CoreShop\Component\Core\Order\Transformer;

use CoreShop\Component\Core\Model\OrderShipmentItemInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentItemTransformerInterface;

final class OrderItemToShipmentItemTransformer implements OrderDocumentItemTransformerInterface
{
    public function __construct(private OrderDocumentItemTransformerInterface $inner)
    {
    }

    public function transform(
        OrderDocumentInterface $orderDocument,
        OrderItemInterface $orderItem,
        OrderDocumentItemInterface $documentItem,
        float $quantity,
        array $options = []
    ): OrderDocumentItemInterface {
        if ($documentItem instanceof OrderShipmentItemInterface && $orderItem instanceof \CoreShop\Component\Core\Model\OrderItemInterface) {
            $documentItem->setWeight($orderItem->getItemWeight() * $quantity);
        }

        return $this->inner->transform($orderDocument, $orderItem, $documentItem, $quantity, $options);
    }
}

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

namespace CoreShop\Component\Core\Order\Transformer;

use CoreShop\Component\Core\Model\OrderShipmentItemInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentItemTransformerInterface;

final class OrderItemToShipmentItemTransformer implements OrderDocumentItemTransformerInterface
{
    public function __construct(
        private OrderDocumentItemTransformerInterface $inner,
    ) {
    }

    public function transform(
        OrderDocumentInterface $orderDocument,
        OrderItemInterface $orderItem,
        OrderDocumentItemInterface $documentItem,
        int $quantity,
        array $options = [],
    ): OrderDocumentItemInterface {
        if ($documentItem instanceof OrderShipmentItemInterface && $orderItem instanceof \CoreShop\Component\Core\Model\OrderItemInterface) {
            $documentItem->setWeight($orderItem->getItemWeight() * $quantity);
        }

        return $this->inner->transform($orderDocument, $orderItem, $documentItem, $quantity, $options);
    }
}

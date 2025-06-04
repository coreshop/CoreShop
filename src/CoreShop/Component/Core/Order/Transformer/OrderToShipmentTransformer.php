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

use CoreShop\Component\Core\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentTransformerInterface;

final class OrderToShipmentTransformer implements OrderDocumentTransformerInterface
{
    public function __construct(
        private OrderDocumentTransformerInterface $inner,
    ) {
    }

    public function transform(
        OrderInterface $order,
        OrderDocumentInterface $document,
        array $itemsToTransform,
    ): OrderDocumentInterface {
        if ($document instanceof OrderShipmentInterface && $order instanceof \CoreShop\Component\Core\Model\OrderInterface) {
            $document->setWeight($order->getWeight());
        }

        return $this->inner->transform($order, $document, $itemsToTransform);
    }
}

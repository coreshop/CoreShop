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

use CoreShop\Component\Core\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentTransformerInterface;

final class OrderToShipmentTransformer implements OrderDocumentTransformerInterface
{
    public function __construct(private OrderDocumentTransformerInterface $inner)
    {
    }

    public function transform(
        OrderInterface $order,
        OrderDocumentInterface $document,
        array $itemsToTransform
    ): OrderDocumentInterface {
        if ($document instanceof OrderShipmentInterface && $order instanceof \CoreShop\Component\Core\Model\OrderInterface) {
            $document->setWeight($order->getWeight());
        }

        return $this->inner->transform($order, $document, $itemsToTransform);
    }
}

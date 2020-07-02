<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
    /**
     * @var OrderDocumentTransformerInterface
     */
    private $inner;

    /**
     * @param OrderDocumentTransformerInterface $inner
     */
    public function __construct(OrderDocumentTransformerInterface $inner)
    {
        $this->inner = $inner;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(OrderInterface $order, OrderDocumentInterface $document, $items)
    {
        if ($document instanceof OrderShipmentInterface && $order instanceof \CoreShop\Component\Core\Model\OrderInterface) {
            $document->setWeight($order->getWeight());
        }

        return $this->inner->transform($order, $document, $items);
    }
}

<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentItemInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use Webmozart\Assert\Assert;

class OrderItemToShipmentItemTransformer implements OrderDocumentItemTransformerInterface
{
    /**
     * @var ObjectServiceInterface
     */
    private $objectService;

    /**
     * @var string
     */
    private $pathForItems;

    /**
     * @var TransformerEventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param ObjectServiceInterface $objectService
     * @param string $pathForItems
     * @param TransformerEventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ObjectServiceInterface $objectService,
        $pathForItems,
        TransformerEventDispatcherInterface $eventDispatcher
    )
    {
        $this->objectService = $objectService;
        $this->pathForItems = $pathForItems;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(OrderDocumentInterface $shipment, OrderItemInterface $orderItem, OrderDocumentItemInterface $shipmentItem, $quantity)
    {
        /**
         * @var $shipment OrderInvoiceInterface
         * @var $orderItem OrderItemInterface
         * @var $shipmentItem OrderShipmentItemInterface
         */
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);
        Assert::isInstanceOf($shipment, OrderDocumentInterface::class);
        Assert::isInstanceOf($shipmentItem, OrderDocumentItemInterface::class);

        $this->eventDispatcher->dispatchPreEvent('shipment_item', $shipmentItem, ['shipment' => $shipment, 'order' => $orderItem->getOrder(), 'order_item' => $orderItem]);

        $itemFolder = $this->objectService->createFolderByPath($shipment->getFullPath().'/'.$this->pathForItems);

        $shipmentItem->setKey($orderItem->getKey());
        $shipmentItem->setParent($itemFolder);
        $shipmentItem->setPublished(true);

        $shipmentItem->setOrderItem($orderItem);
        $shipmentItem->setQuantity($quantity);
        $shipmentItem->setTotal($orderItem->getItemPrice(true) * $quantity, true);
        $shipmentItem->setTotal($orderItem->getItemPrice(false) * $quantity, false);

        $shipmentItem->setBaseTotal($orderItem->getBaseItemPrice(true) * $quantity, true);
        $shipmentItem->setBaseTotal($orderItem->getBaseItemPrice(false) * $quantity, false);

        $shipmentItem->setWeight($orderItem->getTotalWeight());

        VersionHelper::useVersioning(function() use ($shipmentItem) {
            $shipmentItem->save();
        }, false);

        $this->eventDispatcher->dispatchPostEvent('shipment_item', $shipmentItem, ['shipment' => $shipment, 'order' => $orderItem->getOrder(), 'order_item' => $orderItem]);

        return $shipmentItem;
    }
}

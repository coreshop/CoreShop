<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentItemInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use Webmozart\Assert\Assert;

class OrderItemToShipmentItemTransformer implements OrderDocumentItemTransformerInterface
{
    protected FolderCreationServiceInterface $folderCreationService;
    protected TransformerEventDispatcherInterface $eventDispatcher;

    public function __construct(
        FolderCreationServiceInterface $folderCreationService,
        TransformerEventDispatcherInterface $eventDispatcher
    ) {
        $this->folderCreationService = $folderCreationService;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function transform(
        OrderDocumentInterface $shipment,
        OrderItemInterface $orderItem,
        OrderDocumentItemInterface $shipmentItem,
        int $quantity,
        array $options = []
    ): OrderDocumentItemInterface
    {
        /**
         * @var OrderInvoiceInterface      $shipment
         * @var OrderItemInterface         $orderItem
         * @var OrderShipmentItemInterface $shipmentItem
         */
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);
        Assert::isInstanceOf($shipment, OrderDocumentInterface::class);
        Assert::isInstanceOf($shipmentItem, OrderShipmentItemInterface::class);

        $this->eventDispatcher->dispatchPreEvent(
            'shipment_item',
            $shipmentItem,
            [
                'shipment' => $shipment,
                'order' => $orderItem->getOrder(),
                'order_item' => $orderItem,
                'options' => $options,
            ]
        );

        $itemFolder = $this->folderCreationService->createFolderForResource($shipmentItem, ['prefix' => $shipment->getFullPath()]);

        $shipmentItem->setKey($orderItem->getKey());
        $shipmentItem->setParent($itemFolder);
        $shipmentItem->setPublished(true);

        $shipmentItem->setOrderItem($orderItem);
        $shipmentItem->setQuantity($quantity);
        $shipmentItem->setTotal((int)($orderItem->getItemPrice(true) * $quantity), true);
        $shipmentItem->setTotal((int)($orderItem->getItemPrice(false) * $quantity), false);

        $shipmentItem->setConvertedTotal((int)($orderItem->getConvertedItemPrice(true) * $quantity), true);
        $shipmentItem->setConvertedTotal((int)($orderItem->getConvertedItemPrice(false) * $quantity), false);

        VersionHelper::useVersioning(function () use ($shipmentItem) {
            $shipmentItem->save();
        }, false);

        $this->eventDispatcher->dispatchPostEvent(
            'shipment_item',
            $shipmentItem,
            [
                'shipment' => $shipment,
                'order' => $orderItem->getOrder(),
                'order_item' => $orderItem,
                'options' => $options,
            ]
        );

        return $shipmentItem;
    }
}

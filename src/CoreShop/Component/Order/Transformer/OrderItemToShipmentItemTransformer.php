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

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentItemInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use Webmozart\Assert\Assert;

class OrderItemToShipmentItemTransformer implements OrderDocumentItemTransformerInterface
{
    public function __construct(
        protected FolderCreationServiceInterface $folderCreationService,
        protected TransformerEventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function transform(
        OrderDocumentInterface $orderDocument,
        OrderItemInterface $orderItem,
        OrderDocumentItemInterface $documentItem,
        int $quantity,
        array $options = [],
    ): OrderDocumentItemInterface {
        Assert::isInstanceOf($documentItem, OrderShipmentItemInterface::class);

        $this->eventDispatcher->dispatchPreEvent(
            'shipment_item',
            $documentItem,
            [
                'shipment' => $orderDocument,
                'order' => $orderItem->getOrder(),
                'order_item' => $orderItem,
                'options' => $options,
            ],
        );

        $itemFolder = $this->folderCreationService->createFolderForResource($documentItem, ['prefix' => $orderDocument->getFullPath()]);

        $documentItem->setKey($orderItem->getKey());
        $documentItem->setParent($itemFolder);
        $documentItem->setPublished(true);

        $documentItem->setOrderItem($orderItem);
        $documentItem->setQuantity($quantity);
        $documentItem->setTotal($orderItem->getItemPrice(true) * $quantity, true);
        $documentItem->setTotal($orderItem->getItemPrice(false) * $quantity, false);

        $documentItem->setConvertedTotal($orderItem->getConvertedItemPrice(true) * $quantity, true);
        $documentItem->setConvertedTotal($orderItem->getConvertedItemPrice(false) * $quantity, false);

        VersionHelper::useVersioning(function () use ($documentItem) {
            $documentItem->save();
        }, false);

        $this->eventDispatcher->dispatchPostEvent(
            'shipment_item',
            $documentItem,
            [
                'shipment' => $orderDocument,
                'order' => $orderItem->getOrder(),
                'order_item' => $orderItem,
                'options' => $options,
            ],
        );

        return $documentItem;
    }
}

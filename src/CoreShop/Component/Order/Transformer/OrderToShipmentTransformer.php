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

use Carbon\Carbon;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use Pimcore\Model\DataObject\Service;
use Webmozart\Assert\Assert;

class OrderToShipmentTransformer implements OrderDocumentTransformerInterface
{
    public function __construct(
        protected OrderDocumentItemTransformerInterface $orderItemToShipmentItemTransformer,
        protected NumberGeneratorInterface $numberGenerator,
        protected FolderCreationServiceInterface $folderCreationService,
        protected PimcoreRepositoryInterface $orderItemRepository,
        protected PimcoreFactoryInterface $shipmentItemFactory,
        protected TransformerEventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function transform(
        OrderInterface $order,
        OrderDocumentInterface $document,
        array $itemsToTransform,
    ): OrderDocumentInterface {
        Assert::isInstanceOf($document, OrderShipmentInterface::class);

        $this->eventDispatcher->dispatchPreEvent('shipment', $document, ['order' => $order, 'items' => $itemsToTransform]);

        $documentFolder = $this->folderCreationService->createFolderForResource($document, ['prefix' => $order->getFullPath()]);

        $document->setOrder($order);

        $documentNumber = $this->numberGenerator->generate($document);

        /**
         * @var OrderShipmentInterface $document
         * @var OrderInterface $order
         */
        $document->setKey(Service::getValidKey($documentNumber, 'object'));
        $document->setShipmentNumber($documentNumber);
        $document->setParent($documentFolder);
        $document->setPublished(true);
        $document->setShipmentDate(Carbon::now());

        /*
         * We need to save the order twice in order to create the object in the tree for pimcore
         */
        VersionHelper::useVersioning(function () use ($document) {
            $document->save();
        }, false);
        $items = [];

        foreach ($itemsToTransform as $item) {
            $documentItem = $this->shipmentItemFactory->createNew();
            $orderItem = $this->orderItemRepository->find($item['orderItemId']);
            $quantity = $item['quantity'];

            if ($orderItem instanceof OrderItemInterface) {
                $items[] = $this->orderItemToShipmentItemTransformer->transform(
                    $document,
                    $orderItem,
                    $documentItem,
                    (int) $quantity,
                    $item,
                );
            }
        }

        $document->setItems($items);
        VersionHelper::useVersioning(function () use ($document) {
            $document->save();
        }, false);

        $this->eventDispatcher->dispatchPostEvent('shipment', $document, ['order' => $order, 'items' => $itemsToTransform]);

        return $document;
    }
}

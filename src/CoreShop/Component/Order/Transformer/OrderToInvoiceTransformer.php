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
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Order\OrderInvoiceStates;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use Pimcore\Model\DataObject\Service;
use Webmozart\Assert\Assert;

class OrderToInvoiceTransformer implements OrderDocumentTransformerInterface
{
    public function __construct(
        protected OrderDocumentItemTransformerInterface $orderItemToInvoiceItemTransformer,
        protected NumberGeneratorInterface $numberGenerator,
        protected FolderCreationServiceInterface $folderCreationService,
        protected PimcoreRepositoryInterface $orderItemRepository,
        protected PimcoreFactoryInterface $invoiceItemFactory,
        protected OrderInvoiceRepositoryInterface $invoiceRepository,
        protected TransformerEventDispatcherInterface $eventDispatcher,
        protected AdjustmentFactoryInterface $adjustmentFactory,
    ) {
    }

    public function transform(
        OrderInterface $order,
        OrderDocumentInterface $document,
        array $itemsToTransform,
    ): OrderDocumentInterface {
        /**
         * @var OrderInterface $order
         */
        Assert::isInstanceOf($order, OrderInterface::class);
        Assert::isInstanceOf($document, OrderInvoiceInterface::class);

        $this->eventDispatcher->dispatchPreEvent('invoice', $document, ['order' => $order, 'items' => $itemsToTransform]);

        $documentFolder = $this->folderCreationService->createFolderForResource($document, ['prefix' => $order->getFullPath()]);

        $document->setOrder($order);

        $documentNumber = $this->numberGenerator->generate($document);

        /**
         * @var OrderInvoiceInterface $document
         */
        $document->setKey(Service::getValidKey($documentNumber, 'object'));
        $document->setInvoiceNumber($documentNumber);
        $document->setParent($documentFolder);
        $document->setPublished(true);
        $document->setInvoiceDate(Carbon::now());

        /*
         * We need to save the order twice in order to create the object in the tree for pimcore
         */
        VersionHelper::useVersioning(function () use ($document) {
            $document->save();
        }, false);

        $items = [];

        /**
         * @var array<string, mixed> $item
         */
        foreach ($itemsToTransform as $item) {
            Assert::keyExists($item, 'quantity');
            Assert::keyExists($item, 'orderItemId');

            $documentItem = $this->invoiceItemFactory->createNew();
            $orderItem = $this->orderItemRepository->find($item['orderItemId']);
            $quantity = $item['quantity'];

            if ($orderItem instanceof OrderItemInterface) {
                $items[] = $this->orderItemToInvoiceItemTransformer->transform(
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

        $this->calculateInvoice($document);

        $this->eventDispatcher->dispatchPostEvent('invoice', $document, ['order' => $order, 'items' => $itemsToTransform]);

        return $document;
    }

    private function calculateInvoice(OrderInvoiceInterface $document): void
    {
        $this->calculateSubtotal($document, true);
        $this->calculateSubtotal($document, false);
        $this->calculateAdjustments($document, true);
        $this->calculateAdjustments($document, false);
        $this->calculateTotal($document, true);
        $this->calculateTotal($document, false);

        VersionHelper::useVersioning(function () use ($document) {
            $document->save();
        }, false);
    }

    private function calculateSubtotal(OrderInvoiceInterface $document, bool $converted = true): void
    {
        $subtotalWithTax = 0;
        $subtotalWithoutTax = 0;

        /**
         * @var OrderInvoiceItemInterface $item
         */
        foreach ($document->getItems() as $item) {
            if ($converted) {
                $subtotalWithTax += $item->getConvertedTotal();
                $subtotalWithoutTax += $item->getConvertedTotal(false);
            } else {
                $subtotalWithTax += $item->getTotal();
                $subtotalWithoutTax += $item->getTotal(false);
            }
        }

        if ($converted) {
            $document->setConvertedSubtotal($subtotalWithTax);
            $document->setConvertedSubtotal($subtotalWithoutTax, false);
        } else {
            $document->setSubtotal($subtotalWithTax);
            $document->setSubtotal($subtotalWithoutTax, false);
        }
    }

    private function calculateAdjustments(OrderInvoiceInterface $document, bool $converted = true): void
    {
        $order = $document->getOrder();

        foreach ($converted ? $order->getConvertedAdjustments() : $order->getAdjustments() as $adjustment) {
            $orderAdjustmentsGross = $converted ? $order->getConvertedAdjustmentsTotal($adjustment->getTypeIdentifier()) : $order->getAdjustmentsTotal($adjustment->getTypeIdentifier());
            $orderAdjustmentsNet = $converted ? $order->getConvertedAdjustmentsTotal($adjustment->getTypeIdentifier(), false) : $order->getAdjustmentsTotal($adjustment->getTypeIdentifier(), false);

            $adjustmentValueToProcessGross = $orderAdjustmentsGross - $this->getProcessedAdjustmentValue($order, $adjustment->getTypeIdentifier(), true, $converted);
            $adjustmentValueToProcessNet = $orderAdjustmentsNet - $this->getProcessedAdjustmentValue($order, $adjustment->getTypeIdentifier(), false, $converted);

            if (0 !== $adjustmentValueToProcessGross) {
                $newAdjustment = $this->adjustmentFactory->createWithData($adjustment->getTypeIdentifier(), $adjustment->getLabel(), $adjustmentValueToProcessGross, $adjustmentValueToProcessNet, $adjustment->getNeutral());

                $converted ? $document->addConvertedAdjustment($newAdjustment) : $document->addAdjustment($newAdjustment);
            }
        }
    }

    private function calculateTotal(OrderInvoiceInterface $document, bool $converted = true): void
    {
        if ($converted) {
            $subtotalWithTax = $document->getConvertedSubtotal();
            $adjustmentsTotal = $document->getConvertedAdjustmentsTotal();

            $subtotalWithoutTax = $document->getConvertedSubtotal(false);
            $adjustmentsTotalWithoutTax = $document->getConvertedAdjustmentsTotal(null, false);
        } else {
            $subtotalWithTax = $document->getSubtotal();
            $adjustmentsTotal = $document->getAdjustmentsTotal();

            $subtotalWithoutTax = $document->getSubtotal(false);
            $adjustmentsTotalWithoutTax = $document->getAdjustmentsTotal(null, false);
        }

        $total = $subtotalWithTax + $adjustmentsTotal;
        $totalWithoutTax = $subtotalWithoutTax + $adjustmentsTotalWithoutTax;

        if ($converted) {
            $document->setConvertedTotal($total);
            $document->setConvertedTotal($totalWithoutTax, false);
        } else {
            $document->setTotal($total);
            $document->setTotal($totalWithoutTax, false);
        }
    }

    private function getProcessedAdjustmentValue(OrderInterface $order, string $adjustmentIdentifier, bool $withTax, bool $converted): int
    {
        $documents = $this->invoiceRepository->getDocumentsNotInState($order, OrderInvoiceStates::STATE_CANCELLED);
        $processedValue = 0;

        /**
         * @var OrderInvoiceInterface $document
         */
        foreach ($documents as $document) {
            foreach ($converted ? $document->getConvertedAdjustments() : $document->getAdjustments() as $adjustment) {
                if ($adjustment->getTypeIdentifier() === $adjustmentIdentifier) {
                    $processedValue += $adjustment->getAmount($withTax);
                }
            }
        }

        return $processedValue;
    }
}

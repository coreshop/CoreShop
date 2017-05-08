<?php

namespace CoreShop\Component\Order\Processable;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Repository\OrderDocumentRepositoryInterface;

class ProcessableOrderItems implements ProcessableInterface
{
    /**
     * @var OrderDocumentRepositoryInterface
     */
    private $documentsRepository;

    /**
     * @param OrderDocumentRepositoryInterface $documentsRepository
     */
    public function __construct(OrderDocumentRepositoryInterface $documentsRepository)
    {
        $this->documentsRepository = $documentsRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessableItems(OrderInterface $order)
    {
        $items = $order->getItems();
        $processedItems = $this->getProcessedItems($order);
        $processAbleItems = [];

        foreach ($items as $item) {
            if ($item instanceof OrderItemInterface) {
                if (array_key_exists($item->getId(), $processedItems)) {
                    if ($processedItems[$item->getId()]['quantity'] < $item->getQuantity()) {
                        $processAbleItems[$item->getId()] = [
                            "quantity" => $item->getQuantity() - $processedItems[$item->getId()]['amount'],
                            "item" => $item,
                            "orderItemId" => $item->getId()
                        ];
                    }
                } else {
                    $processAbleItems[$item->getId()] = [
                        "quantity" => $item->getQuantity(),
                        "item" => $item,
                        "orderItemId" => $item->getId()
                    ];
                }
            }
        }

        return $processAbleItems;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessedItems(OrderInterface $order)
    {
        $documents = $this->documentsRepository->getDocuments($order);
        $processedItems = [];

        foreach ($documents as $document) {
            foreach ($document->getItems() as $processedItem) {
                $orderItem = $processedItem->getOrderItem();

                if ($orderItem instanceof OrderItemInterface) {
                    if (array_key_exists($orderItem->getId(), $processedItems)) {
                        $processedItems[$orderItem->getId()]['quantity'] += $processedItem->getQuantity();
                    } else {
                        $processedItems[$orderItem->getId()] = [
                            'quantity' => $processedItem->getQuantity(),
                            'orderItem' => $orderItem
                        ];
                    }
                }
            }
        }

        return $processedItems;
    }

    /**
     * {@inheritdoc}
     */
    public function isFullyProcessed(OrderInterface $order)
    {
        return count($this->getProcessableItems($order)) === 0;
    }
}
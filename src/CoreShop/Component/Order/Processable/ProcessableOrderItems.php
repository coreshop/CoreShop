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

namespace CoreShop\Component\Order\Processable;

use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Repository\OrderDocumentRepositoryInterface;

class ProcessableOrderItems implements ProcessableInterface
{
    protected OrderDocumentRepositoryInterface $documentsRepository;
    protected string $stateCancelled;

    public function __construct(OrderDocumentRepositoryInterface $documentsRepository, string $stateCancelled)
    {
        $this->documentsRepository = $documentsRepository;
        $this->stateCancelled = $stateCancelled;
    }

    public function getProcessableItems(OrderInterface $order): array
    {
        $items = $order->getItems();
        $processedItems = $this->getProcessedItems($order);
        $processAbleItems = [];

        foreach ($items as $item) {
            if ($item instanceof OrderItemInterface) {
                if (array_key_exists($item->getId(), $processedItems)) {
                    if ($processedItems[$item->getId()]['quantity'] < $item->getQuantity()) {
                        $processAbleItems[$item->getId()] = [
                            'quantity' => $item->getQuantity() - $processedItems[$item->getId()]['quantity'],
                            'item' => $item,
                            'orderItemId' => $item->getId(),
                        ];
                    }
                } else {
                    $processAbleItems[$item->getId()] = [
                        'quantity' => $item->getQuantity(),
                        'item' => $item,
                        'orderItemId' => $item->getId(),
                    ];
                }
            }
        }

        return $processAbleItems;
    }

    public function getProcessedItems(OrderInterface $order): array
    {
        $documents = $this->documentsRepository->getDocumentsNotInState($order, $this->stateCancelled);
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
                            'orderItem' => $orderItem,
                        ];
                    }
                }
            }
        }

        return $processedItems;
    }

    public function isFullyProcessed(OrderInterface $order): bool
    {
        return count($this->getProcessableItems($order)) === 0;
    }

    public function isProcessable(OrderInterface $order): bool
    {
        return !$this->isFullyProcessed($order) && $order->getOrderState() !== OrderStates::STATE_CANCELLED;
    }
}

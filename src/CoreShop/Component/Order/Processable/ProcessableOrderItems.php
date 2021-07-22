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

namespace CoreShop\Component\Order\Processable;

use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Repository\OrderDocumentRepositoryInterface;

class ProcessableOrderItems implements ProcessableInterface
{
    /**
     * @var OrderDocumentRepositoryInterface
     */
    protected $documentsRepository;

    /**
     * @var string
     */
    protected $stateCancelled;

    /**
     * @param OrderDocumentRepositoryInterface $documentsRepository
     * @param string                           $stateCancelled
     */
    public function __construct(OrderDocumentRepositoryInterface $documentsRepository, $stateCancelled)
    {
        $this->documentsRepository = $documentsRepository;
        $this->stateCancelled = $stateCancelled;
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

    /**
     * {@inheritdoc}
     */
    public function getProcessedItems(OrderInterface $order)
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

    /**
     * {@inheritdoc}
     */
    public function isFullyProcessed(OrderInterface $order)
    {
        return count($this->getProcessableItems($order)) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isProcessable(OrderInterface $order)
    {
        return !$this->isFullyProcessed($order) && $order->getOrderState() !== OrderStates::STATE_CANCELLED;
    }
}

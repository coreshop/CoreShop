<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use Webmozart\Assert\Assert;

class OrderItemToInvoiceItemTransformer implements OrderDocumentItemTransformerInterface
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
     * @param ObjectServiceInterface              $objectService
     * @param string                              $pathForItems
     * @param TransformerEventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ObjectServiceInterface $objectService,
        $pathForItems,
        TransformerEventDispatcherInterface $eventDispatcher
    ) {
        $this->objectService = $objectService;
        $this->pathForItems = $pathForItems;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(OrderDocumentInterface $invoice, OrderItemInterface $orderItem, OrderDocumentItemInterface $invoiceItem, $quantity)
    {
        /**
         * @var OrderInvoiceInterface     $invoice
         * @var OrderItemInterface        $orderItem
         * @var OrderInvoiceItemInterface $invoiceItem
         */
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);
        Assert::isInstanceOf($invoice, OrderDocumentInterface::class);
        Assert::isInstanceOf($invoiceItem, OrderInvoiceItemInterface::class);

        $this->eventDispatcher->dispatchPreEvent('invoice_item', $invoiceItem, ['invoice' => $invoice, 'order' => $orderItem->getOrder(), 'order_item' => $orderItem]);

        $itemFolder = $this->objectService->createFolderByPath($invoice->getFullPath() . '/' . $this->pathForItems);

        $invoiceItem->setKey($orderItem->getKey());
        $invoiceItem->setParent($itemFolder);
        $invoiceItem->setPublished(true);

        $invoiceItem->setOrderItem($orderItem);
        $invoiceItem->setQuantity($quantity);

        $invoiceItem->setTotal($orderItem->getItemPrice(true) * $quantity, true);
        $invoiceItem->setTotal($orderItem->getItemPrice(false) * $quantity, false);

        $invoiceItem->setBaseTotal($orderItem->getBaseItemPrice(true) * $quantity, true);
        $invoiceItem->setBaseTotal($orderItem->getBaseItemPrice(false) * $quantity, false);

        VersionHelper::useVersioning(function () use ($invoiceItem) {
            $invoiceItem->save();
        }, false);

        $this->eventDispatcher->dispatchPostEvent('invoice_item', $invoiceItem, ['invoice' => $invoice, 'order' => $orderItem->getOrder(), 'order_item' => $orderItem]);

        return $invoiceItem;
    }
}

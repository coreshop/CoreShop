<?php

namespace CoreShop\Bundle\CoreBundle\Order\Transformer;

use CoreShop\Component\Core\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderDocumentItemInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentItemTransformerInterface;
use CoreShop\Component\Order\Transformer\ProposalItemTransformerInterface;
use Pimcore\Model\Object\Fieldcollection;
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

        $itemFolder = $this->objectService->createFolderByPath($shipment->getFullPath() . '/' . $this->pathForItems);

        $shipmentItem->setKey($orderItem->getKey());
        $shipmentItem->setParent($itemFolder);
        $shipmentItem->setPublished(true);

        $shipmentItem->setOrderItem($orderItem);
        $shipmentItem->setQuantity($quantity);
        $shipmentItem->setTotal($orderItem->getItemPrice(true) * $quantity, true);
        $shipmentItem->setTotal($orderItem->getItemPrice(false) * $quantity, false);
        $shipmentItem->setWeight($orderItem->getTotalWeight());

        $shipmentItem->save();

        $this->eventDispatcher->dispatchPostEvent('shipment_item', $shipmentItem, ['shipment' => $shipment, 'order' => $orderItem->getOrder(), 'order_item' => $orderItem]);

        return $shipmentItem;
    }
}
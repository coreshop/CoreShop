<?php

namespace CoreShop\Bundle\OrderBundle\Transformer;

use Carbon\Carbon;
use CoreShop\Component\Core\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentItemTransformerInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentTransformerInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Transformer\ItemKeyTransformerInterface;
use Webmozart\Assert\Assert;

class OrderToShipmentTransformer implements OrderDocumentTransformerInterface
{
    /**
     * @var OrderDocumentItemTransformerInterface
     */
    protected $orderItemToShipmentItemTransformer;

    /**
     * @var ItemKeyTransformerInterface
     */
    protected $keyTransformer;

    /**
     * @var NumberGeneratorInterface
     */
    protected $numberGenerator;

    /**
     * @var string
     */
    protected $shipmentFolderPath;

    /**
     * @var ObjectServiceInterface
     */
    protected $objectService;

    /**
     * @var PimcoreRepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var PimcoreFactoryInterface
     */
    protected $shipmentItemFactory;

    /**
     * @var TransformerEventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param OrderDocumentItemTransformerInterface $orderItemToShipmentItemTransformer
     * @param ItemKeyTransformerInterface $keyTransformer
     * @param NumberGeneratorInterface $numberGenerator
     * @param string $shipmentFolderPath
     * @param ObjectServiceInterface $objectService
     * @param PimcoreRepositoryInterface $orderItemRepository
     * @param PimcoreFactoryInterface $shipmentItemFactory
     * @param TransformerEventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        OrderDocumentItemTransformerInterface $orderItemToShipmentItemTransformer,
        ItemKeyTransformerInterface $keyTransformer,
        NumberGeneratorInterface $numberGenerator,
        $shipmentFolderPath,
        ObjectServiceInterface $objectService,
        PimcoreRepositoryInterface $orderItemRepository,
        PimcoreFactoryInterface $shipmentItemFactory,
        TransformerEventDispatcherInterface $eventDispatcher
    )
    {
        $this->orderItemToShipmentItemTransformer = $orderItemToShipmentItemTransformer;
        $this->keyTransformer = $keyTransformer;
        $this->numberGenerator = $numberGenerator;
        $this->shipmentFolderPath = $shipmentFolderPath;
        $this->objectService = $objectService;
        $this->orderItemRepository = $orderItemRepository;
        $this->shipmentItemFactory = $shipmentItemFactory;
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * {@inheritdoc}
     */
    public function transform(OrderInterface $order, OrderDocumentInterface $shipment, $itemsToTransform)
    {
        /**
         * @var $cart CartInterface
         */
        Assert::isInstanceOf($order, OrderInterface::class);
        Assert::isInstanceOf($shipment, OrderShipmentInterface::class);

        $this->eventDispatcher->dispatchPreEvent('shipment', $shipment, ['order' => $order, 'items' => $itemsToTransform]);

        $shipmentFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $order->getFullPath(), $this->shipmentFolderPath));

        $shipmentNumber = $this->numberGenerator->generate($shipment);
        /**
         * @var $shipment OrderShipmentInterface
         * @var $order OrderInterface
         */
        $shipment->setKey($this->keyTransformer->transform($shipmentNumber));
        $shipment->setShipmentNumber($shipmentNumber);
        $shipment->setParent($shipmentFolder);
        $shipment->setPublished(true);
        $shipment->setShipmentDate(Carbon::now());
        $shipment->setOrder($order);
        $shipment->setWeight($order->getTotalWeight());

        /**
         * We need to save the order twice in order to create the object in the tree for pimcore
         */
        $shipment->save();
        $items = [];

        /**
         * @var $cartItem CartItemInterface
         */
        foreach ($itemsToTransform as $item) {
            $shipmentItem = $this->shipmentItemFactory->createNew();
            $orderItem = $this->orderItemRepository->find($item['orderItemId']);
            $quantity = $item['quantity'];

            if ($orderItem instanceof OrderItemInterface) {
                $items[] = $this->orderItemToShipmentItemTransformer->transform($shipment, $orderItem, $shipmentItem, $quantity);
            }
        }

        $shipment->setItems($items);
        $shipment->save();

        $this->eventDispatcher->dispatchPostEvent('shipment', $shipment, ['order' => $order, 'items' => $itemsToTransform]);

        return $shipment;
    }
}
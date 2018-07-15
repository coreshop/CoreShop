<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Transformer;

use Carbon\Carbon;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Transformer\ItemKeyTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
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
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param OrderDocumentItemTransformerInterface $orderItemToShipmentItemTransformer
     * @param ItemKeyTransformerInterface $keyTransformer
     * @param NumberGeneratorInterface $numberGenerator
     * @param string $shipmentFolderPath
     * @param ObjectServiceInterface $objectService
     * @param PimcoreRepositoryInterface $orderItemRepository
     * @param PimcoreFactoryInterface $shipmentItemFactory
     * @param TransformerEventDispatcherInterface $eventDispatcher
     * @param ObjectManager $objectManager
     */
    public function __construct(
        OrderDocumentItemTransformerInterface $orderItemToShipmentItemTransformer,
        ItemKeyTransformerInterface $keyTransformer,
        NumberGeneratorInterface $numberGenerator,
        $shipmentFolderPath,
        ObjectServiceInterface $objectService,
        PimcoreRepositoryInterface $orderItemRepository,
        PimcoreFactoryInterface $shipmentItemFactory,
        TransformerEventDispatcherInterface $eventDispatcher,
        ObjectManager $objectManager
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
        $this->objectManager = $objectManager;
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

        $shipment->setOrder($order);

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
        $shipment->setWeight($order->getWeight());

        $this->objectManager->persist($shipment);

        /*
         * We need to save the order twice in order to create the object in the tree for pimcore
         */
        VersionHelper::useVersioning(function() {
            $this->objectManager->flush();
        }, false);
        $items = [];

        /*
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

        $this->objectManager->persist($shipment);

        VersionHelper::useVersioning(function() {
            $this->objectManager->flush();
        }, false);

        $this->eventDispatcher->dispatchPostEvent('shipment', $shipment, ['order' => $order, 'items' => $itemsToTransform]);

        return $shipment;
    }
}

<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Transformer;

use Carbon\Carbon;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Model\DataObject\Service;
use Webmozart\Assert\Assert;

class OrderToShipmentTransformer implements OrderDocumentTransformerInterface
{
    protected $orderItemToShipmentItemTransformer;
    protected $numberGenerator;
    protected $shipmentFolderPath;
    protected $objectService;
    protected $orderItemRepository;
    protected $shipmentItemFactory;
    protected $eventDispatcher;

    public function __construct(
        OrderDocumentItemTransformerInterface $orderItemToShipmentItemTransformer,
        NumberGeneratorInterface $numberGenerator,
        string $shipmentFolderPath,
        ObjectServiceInterface $objectService,
        PimcoreRepositoryInterface $orderItemRepository,
        PimcoreFactoryInterface $shipmentItemFactory,
        TransformerEventDispatcherInterface $eventDispatcher
    ) {
        $this->orderItemToShipmentItemTransformer = $orderItemToShipmentItemTransformer;
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
         * @var $cart OrderInterface
         */
        Assert::isInstanceOf($order, OrderInterface::class);
        Assert::isInstanceOf($shipment, OrderShipmentInterface::class);

        $this->eventDispatcher->dispatchPreEvent('shipment', $shipment, ['order' => $order, 'items' => $itemsToTransform]);

        $shipmentFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $order->getFullPath(), $this->shipmentFolderPath));

        $shipment->setOrder($order);

        $shipmentNumber = $this->numberGenerator->generate($shipment);

        /**
         * @var $shipment OrderShipmentInterface
         * @var $order    OrderInterface
         */
        $shipment->setKey(Service::getValidKey($shipmentNumber, 'object'));
        $shipment->setShipmentNumber($shipmentNumber);
        $shipment->setParent($shipmentFolder);
        $shipment->setPublished(true);
        $shipment->setShipmentDate(Carbon::now());

        /*
         * We need to save the order twice in order to create the object in the tree for pimcore
         */
        VersionHelper::useVersioning(function () use ($shipment) {
            $shipment->save();
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
                $items[] = $this->orderItemToShipmentItemTransformer->transform(
                    $shipment,
                    $orderItem,
                    $shipmentItem,
                    $quantity,
                    $item
                );
            }
        }

        $shipment->setItems($items);
        VersionHelper::useVersioning(function () use ($shipment) {
            $shipment->save();
        }, false);

        $this->eventDispatcher->dispatchPostEvent('shipment', $shipment, ['order' => $order, 'items' => $itemsToTransform]);

        return $shipment;
    }
}

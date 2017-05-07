<?php

namespace CoreShop\Component\Core\Transformer;

use Carbon\Carbon;
use CoreShop\Component\Core\Context\LocaleContextInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleOrderProcessorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderInvoiceItemInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Order\Repository\OrderInvoiceRepositoryInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentItemTransformerInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentTransformerInterface;
use CoreShop\Component\Order\Transformer\ProposalItemTransformerInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Transformer\ItemKeyTransformerInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Pimcore\Model\Object\Fieldcollection;
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
     * @param OrderDocumentItemTransformerInterface $orderItemToShipmentItemTransformer
     * @param ItemKeyTransformerInterface $keyTransformer
     * @param NumberGeneratorInterface $numberGenerator
     * @param string $shipmentFolderPath
     * @param ObjectServiceInterface $objectService
     * @param PimcoreRepositoryInterface $orderItemRepository
     * @param PimcoreFactoryInterface $shipmentItemFactory
     */
    public function __construct(
        OrderDocumentItemTransformerInterface $orderItemToShipmentItemTransformer,
        ItemKeyTransformerInterface $keyTransformer,
        NumberGeneratorInterface $numberGenerator,
        $shipmentFolderPath,
        ObjectServiceInterface $objectService,
        PimcoreRepositoryInterface $orderItemRepository,
        PimcoreFactoryInterface $shipmentItemFactory
    )
    {
        $this->orderItemToShipmentItemTransformer = $orderItemToShipmentItemTransformer;
        $this->keyTransformer = $keyTransformer;
        $this->numberGenerator = $numberGenerator;
        $this->shipmentFolderPath = $shipmentFolderPath;
        $this->objectService = $objectService;
        $this->orderItemRepository = $orderItemRepository;
        $this->shipmentItemFactory = $shipmentItemFactory;
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

        return $shipment;
    }
}
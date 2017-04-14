<?php

namespace CoreShop\Component\Core\Transformer;

use CoreShop\Component\Core\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Transformer\ProposalItemTransformerInterface;
use Webmozart\Assert\Assert;

class CartItemToOrderItemTransformer implements ProposalItemTransformerInterface
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
     * @param ObjectServiceInterface $objectService
     * @param string $pathForItems
     */
    public function __construct(
        ObjectServiceInterface $objectService,
        $pathForItems
    )
    {
        $this->objectService = $objectService;
        $this->pathForItems = $pathForItems;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(ProposalInterface $order, ProposalItemInterface $cartItem, ProposalItemInterface $orderItem)
    {
        /**
         * @var $order OrderInterface
         * @var $cartItem CartItemInterface
         * @var $orderItem OrderItemInterface
         */
        Assert::isInstanceOf($cartItem, CartItemInterface::class);
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);
        Assert::isInstanceOf($order, OrderInterface::class);

        $itemFolder = $this->objectService->createFolderByPath($order->getFullPath() . '/' . $this->pathForItems);

        $this->objectService->copyObject($cartItem, $orderItem);

        $orderItem->setKey($cartItem->getKey());
        $orderItem->setParent($itemFolder);
        $orderItem->setPublished(true);

        $orderItem->setProduct($cartItem->getProduct());
        $orderItem->setItemWholesalePrice($cartItem->getItemWholesalePrice());
        $orderItem->setItemRetailPrice($cartItem->getItemRetailPrice(true), true);
        $orderItem->setItemRetailPrice($cartItem->getItemRetailPrice(false), false);
        $orderItem->setTotal($cartItem->getTotal(true), true);
        $orderItem->setTotal($cartItem->getTotal(false), false);
        $orderItem->setItemPrice($cartItem->getItemPrice(true), true);
        $orderItem->setItemPrice($cartItem->getItemPrice(false), false);

        $orderItem->save();

        $order->addItem($orderItem);
        //TODO: Collect all Taxes
    }
}
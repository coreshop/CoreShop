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
 *
*/

namespace CoreShop\Bundle\OrderBundle\Transformer;

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

        $this->eventDispatcher->dispatchPreEvent('order_item', $cartItem, ['order' => $order, 'cart' => $cartItem->getCart(), 'order_item' => $orderItem]);

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
        $orderItem->setTotalTax($cartItem->getTotalTax());
        $orderItem->setItemTax($cartItem->getItemTax());
        $orderItem->setItemWeight($cartItem->getItemWeight());
        $orderItem->setTotalWeight($cartItem->getTotalWeight());

        $orderItem->save();

        $order->addItem($orderItem);
        //TODO: Collect all Taxes

        $this->eventDispatcher->dispatchPostEvent('order_item', $cartItem, ['order' => $order, 'cart' => $cartItem->getCart(), 'order_item' => $orderItem]);
    }
}
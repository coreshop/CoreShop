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

namespace CoreShop\Component\Core\Order\Committer;

use Carbon\Carbon;
use CoreShop\Bundle\WorkflowBundle\Applier\StateMachineApplierInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Committer\OrderCommitterInterface;
use CoreShop\Component\Order\Committer\QuoteCommitterInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Order\OrderInvoiceStates;
use CoreShop\Component\Order\OrderPaymentStates;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Order\OrderShipmentStates;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Pimcore\DataObject\ObjectClonerInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Transformer\ItemKeyTransformerInterface;
use Pimcore\Model\DataObject\Folder;
use Webmozart\Assert\Assert;

class OrderCommitter implements OrderCommitterInterface, QuoteCommitterInterface
{
    /**
     * @var CartManagerInterface
     */
    protected $cartManager;

    /**
     * @var ObjectServiceInterface
     */
    protected $objectService;

    /**
     * @var NumberGeneratorInterface
     */
    protected $numberGenerator;

    /**
     * @var ObjectClonerInterface
     */
    protected $objectCloner;

    /**
     * @var ItemKeyTransformerInterface
     */
    protected $keyTransformer;

    /**
     * @var StateMachineApplierInterface
     */
    private $stateMachineApplier;

    /**
     * @var string
     */
    protected $orderFolderPath;

    public function __construct(
        CartManagerInterface $cartManager,
        ObjectServiceInterface $objectService,
        NumberGeneratorInterface $numberGenerator,
        ObjectClonerInterface $objectCloner,
        ItemKeyTransformerInterface $keyTransformer,
        StateMachineApplierInterface $stateMachineApplier,
        string $orderFolderPath
    ) {
        $this->cartManager = $cartManager;
        $this->objectService = $objectService;
        $this->numberGenerator = $numberGenerator;
        $this->objectCloner = $objectCloner;
        $this->keyTransformer = $keyTransformer;
        $this->stateMachineApplier = $stateMachineApplier;
        $this->orderFolderPath = $orderFolderPath;
    }

    public function commitOrder(OrderInterface $order): void
    {
        /**
         * @var \CoreShop\Component\Core\Model\OrderInterface $order
         */
        Assert::isInstanceOf($order, \CoreShop\Component\Core\Model\OrderInterface::class);

        $orderFolder = $this->objectService->createFolderByPath(
            sprintf(
                '%s/%s',
                $this->orderFolderPath,
                date('Y/m/d')
            )
        );
        $orderNumber = $this->numberGenerator->generate($order);

        $order->setParent($orderFolder);
        $order->setSaleState(OrderSaleStates::STATE_ORDER);
        $order->setOrderDate(Carbon::now());
        $order->setOrderNumber($orderNumber);
        $order->setKey($this->keyTransformer->transform($orderNumber));
        $order->setOrderState(OrderStates::STATE_INITIALIZED);
        $order->setShippingState(OrderShipmentStates::STATE_NEW);
        $order->setPaymentState(OrderPaymentStates::STATE_NEW);
        $order->setInvoiceState(OrderInvoiceStates::STATE_NEW);

        $this->cartManager->persistCart($order);

        $originalShippingAddress = $order->hasShippableItems() === false ? $order->getInvoiceAddress() : $order->getShippingAddress();

        /**
         * @var AddressInterface $shippingAddress
         */
        $shippingAddress = $this->objectCloner->cloneObject(
            $originalShippingAddress,
            $this->objectService->createFolderByPath(sprintf('%s/addresses', $order->getFullPath())),
            'shipping',
            false
        );
        /**
         * @var AddressInterface $invoiceAddress
         */
        $invoiceAddress = $this->objectCloner->cloneObject(
            $order->getInvoiceAddress(),
            $this->objectService->createFolderByPath(sprintf('%s/addresses', $order->getFullPath())),
            'invoice',
            false
        );

        VersionHelper::useVersioning(function () use ($shippingAddress, $invoiceAddress) {
            $shippingAddress->save();
            $invoiceAddress->save();
        }, false);

        $order->setShippingAddress($shippingAddress);
        $order->setInvoiceAddress($invoiceAddress);

        $this->cartManager->persistCart($order);

        $this->stateMachineApplier->apply($order, OrderTransitions::IDENTIFIER, OrderTransitions::TRANSITION_CREATE);
    }
}

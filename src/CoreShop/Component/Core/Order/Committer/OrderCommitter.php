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

declare(strict_types=1);

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
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use CoreShop\Component\Resource\TokenGenerator\UniqueTokenGenerator;
use Pimcore\Model\DataObject\Service;
use Webmozart\Assert\Assert;

class OrderCommitter implements OrderCommitterInterface, QuoteCommitterInterface
{
    public function __construct(protected CartManagerInterface $cartManager, protected FolderCreationServiceInterface $folderCreationService, protected NumberGeneratorInterface $numberGenerator, protected ObjectClonerInterface $objectCloner, protected StateMachineApplierInterface $stateMachineApplier)
    {
    }

    public function commitOrder(OrderInterface $order): void
    {
        /*
         * @var \CoreShop\Component\Core\Model\OrderInterface $order
         */
        Assert::isInstanceOf($order, \CoreShop\Component\Core\Model\OrderInterface::class);

        $orderFolder = $this->folderCreationService->createFolderForResource($order, [
            'suffix' => date('Y/m/d'),
            'path' => 'order',
        ]);
        $orderNumber = $this->numberGenerator->generate($order);

        $order->setParent($orderFolder);
        $order->setSaleState(OrderSaleStates::STATE_ORDER);
        $order->setOrderDate(Carbon::now());
        $order->setOrderNumber($orderNumber);
        $order->setKey(Service::getValidKey($orderNumber, 'object'));
        $order->setOrderState(OrderStates::STATE_INITIALIZED);
        $order->setShippingState(OrderShipmentStates::STATE_NEW);
        $order->setPaymentState(OrderPaymentStates::STATE_NEW);
        $order->setInvoiceState(OrderInvoiceStates::STATE_NEW);

        $tokenGenerator = new UniqueTokenGenerator();
        $order->setToken($tokenGenerator->generate(10));

        $this->cartManager->persistCart($order);

        $originalShippingAddress = false === $order->hasShippableItems() ? $order->getInvoiceAddress() : $order->getShippingAddress();

        /**
         * @var AddressInterface $shippingAddress
         * @psalm-suppress InvalidArgument
         */
        $shippingAddress = $this->objectCloner->cloneObject(
            $originalShippingAddress,
            $this->folderCreationService->createFolderForResource($originalShippingAddress, ['prefix' => $order->getFullPath()]),
            'shipping',
            false
        );
        /**
         * @var AddressInterface $invoiceAddress
         * @psalm-suppress InvalidArgument
         */
        $invoiceAddress = $this->objectCloner->cloneObject(
            $order->getInvoiceAddress(),
            $this->folderCreationService->createFolderForResource($order->getInvoiceAddress(), ['prefix' => $order->getFullPath()]),
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

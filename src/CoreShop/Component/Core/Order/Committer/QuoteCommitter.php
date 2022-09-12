<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\Order\Committer;

use Carbon\Carbon;
use CoreShop\Bundle\WorkflowBundle\Applier\StateMachineApplierInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Committer\QuoteCommitterInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Order\QuoteTransitions;
use CoreShop\Component\Pimcore\DataObject\ObjectClonerInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use Pimcore\Model\DataObject\Service;
use Webmozart\Assert\Assert;

class QuoteCommitter implements QuoteCommitterInterface
{
    protected string $orderFolderPath;

    public function __construct(
        protected CartManagerInterface $cartManager,
        protected FolderCreationServiceInterface $folderCreationService,
        protected NumberGeneratorInterface $numberGenerator,
        protected ObjectClonerInterface $objectCloner,
        protected StateMachineApplierInterface $stateMachineApplier,
    ) {
    }

    public function commitOrder(OrderInterface $order): void
    {
        /**
         * @var \CoreShop\Component\Core\Model\OrderInterface $order
         */
        Assert::isInstanceOf($order, \CoreShop\Component\Core\Model\OrderInterface::class);

        $orderFolder = $this->folderCreationService->createFolderForResource($order, [
            'suffix' => date('Y/m/d'),
            'path' => 'quote',
        ]);
        $quoteNumber = $this->numberGenerator->generate($order);

        $order->setParent($orderFolder);
        $order->setSaleState(OrderSaleStates::STATE_QUOTE);
        $order->setOrderDate(Carbon::now());
        $order->setQuoteNumber($quoteNumber);
        $order->setKey(Service::getValidKey($quoteNumber, 'object'));

        $this->cartManager->persistCart($order);

        $originalShippingAddress = $order->hasShippableItems() === false ? $order->getInvoiceAddress() : $order->getShippingAddress();

        /**
         * @var AddressInterface $shippingAddress
         *
         * @psalm-suppress InvalidArgument
         */
        $shippingAddress = $this->objectCloner->cloneObject(
            $originalShippingAddress,
            $this->folderCreationService->createFolderForResource($originalShippingAddress, ['prefix' => $order->getFullPath()]),
            'shipping',
            false,
        );
        /**
         * @var AddressInterface $invoiceAddress
         *
         * @psalm-suppress InvalidArgument
         */
        $invoiceAddress = $this->objectCloner->cloneObject(
            $order->getInvoiceAddress(),
            $this->folderCreationService->createFolderForResource($order->getInvoiceAddress(), ['prefix' => $order->getFullPath()]),
            'invoice',
            false,
        );

        VersionHelper::useVersioning(function () use ($shippingAddress, $invoiceAddress) {
            $shippingAddress->save();
            $invoiceAddress->save();
        }, false);

        $order->setShippingAddress($shippingAddress);
        $order->setInvoiceAddress($invoiceAddress);

        $this->cartManager->persistCart($order);

        $this->stateMachineApplier->apply($order, QuoteTransitions::IDENTIFIER, QuoteTransitions::TRANSITION_CREATE);
    }
}

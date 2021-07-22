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
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Committer\OrderCommitterInterface;
use CoreShop\Component\Order\Committer\QuoteCommitterInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Pimcore\DataObject\ObjectClonerInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use Pimcore\Model\DataObject\Service;
use Webmozart\Assert\Assert;

class QuoteCommitter implements OrderCommitterInterface, QuoteCommitterInterface
{
    protected CartManagerInterface $cartManager;
    protected FolderCreationServiceInterface $folderCreationService;
    protected NumberGeneratorInterface $numberGenerator;
    protected ObjectClonerInterface $objectCloner;
    protected string $orderFolderPath;

    public function __construct(
        CartManagerInterface $cartManager,
        FolderCreationServiceInterface $folderCreationService,
        NumberGeneratorInterface $numberGenerator,
        ObjectClonerInterface $objectCloner,
    ) {
        $this->cartManager = $cartManager;
        $this->folderCreationService = $folderCreationService;
        $this->numberGenerator = $numberGenerator;
        $this->objectCloner = $objectCloner;
    }

    public function commitOrder(OrderInterface $order): void
    {
        /**
         * @var \CoreShop\Component\Core\Model\OrderInterface $order
         */
        Assert::isInstanceOf($order, \CoreShop\Component\Core\Model\OrderInterface::class);

        $orderFolder = $this->folderCreationService->createFolderForResource($order, [
            'suffix' => date('Y/m/d'),
            'path' => 'quote'
        ]);
        $orderNumber = $this->numberGenerator->generate($order);

        $order->setParent($orderFolder);
        $order->setSaleState(OrderSaleStates::STATE_QUOTE);
        $order->setOrderDate(Carbon::now());
        $order->setOrderNumber($orderNumber);
        $order->setKey(Service::getValidKey($orderNumber, 'object'));

        $this->cartManager->persistCart($order);

        $originalShippingAddress = $order->hasShippableItems() === false ? $order->getInvoiceAddress() : $order->getShippingAddress();

        /**
         * @var AddressInterface $shippingAddress
         */
        $shippingAddress = $this->objectCloner->cloneObject(
            $originalShippingAddress,
            $this->folderCreationService->createFolderForResource($originalShippingAddress, ['prefix' => $order->getFullPath()]),
            'shipping',
            false
        );
        /**
         * @var AddressInterface $invoiceAddress
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
    }
}

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

namespace CoreShop\Component\Order\Committer;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Manager\CartManagerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\NumberGenerator\NumberGeneratorInterface;
use CoreShop\Component\Order\OrderSaleStates;
use CoreShop\Component\Pimcore\DataObject\ObjectClonerInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;

class OrderCommitter implements OrderCommitterInterface
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
     * @var string
     */
    protected $orderFolderPath;

    public function __construct(
        CartManagerInterface $cartManager,
        ObjectServiceInterface $objectService,
        NumberGeneratorInterface $numberGenerator,
        ObjectClonerInterface $objectCloner,
        string $orderFolderPath
    ) {
        $this->cartManager = $cartManager;
        $this->objectService = $objectService;
        $this->numberGenerator = $numberGenerator;
        $this->objectCloner = $objectCloner;
        $this->orderFolderPath = $orderFolderPath;
    }

    public function commitOrder(OrderInterface $order): void
    {
        $orderFolder = $this->objectService->createFolderByPath(sprintf('%s/%s', $this->orderFolderPath, date('Y/m/d')));
        $orderNumber = $this->numberGenerator->generate($order);

        $order->setParent($orderFolder);
        $order->setKey($orderNumber);
        $order->setOrderNumber($orderNumber);
        $order->setSaleState(OrderSaleStates::STATE_ORDER);

        /**
         * @var AddressInterface $shippingAddress
         */
        $shippingAddress = $this->objectCloner->cloneObject(
            method_exists($order, 'hasShippableItems') && $order->hasShippableItems() === false ? $order->getInvoiceAddress() : $order->getShippingAddress(),
            $this->objectService->createFolderByPath(sprintf('%s/addresses', $order->getFullPath())),
            'shipping'
        );
        /**
         * @var AddressInterface $invoiceAddress
         */
        $invoiceAddress = $this->objectCloner->cloneObject(
            $order->getInvoiceAddress(),
            $this->objectService->createFolderByPath(sprintf('%s/addresses', $order->getFullPath())),
            'invoice'
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

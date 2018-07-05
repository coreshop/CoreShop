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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Bundle\WorkflowBundle\Applier\StateMachineApplier;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\InvoiceTransitions;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Repository\OrderDocumentRepositoryInterface;
use CoreShop\Component\Order\Transformer\OrderDocumentTransformerInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

final class OrderInvoiceContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var OrderDocumentTransformerInterface
     */
    private $invoiceTransformer;

    /**
     * @var FactoryInterface
     */
    private $orderInvoiceFactory;

    /**
     * @var OrderDocumentRepositoryInterface
     */
    private $orderInvoiceRepository;

    /**
     * @var StateMachineApplier
     */
    private $stateMachineApplier;

    /**
     * @param SharedStorageInterface            $sharedStorage
     * @param OrderDocumentTransformerInterface $invoiceTransformer
     * @param FactoryInterface                  $orderInvoiceFactory
     * @param OrderDocumentRepositoryInterface  $orderInvoiceRepository
     * @param StateMachineApplier               $stateMachineApplier
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        OrderDocumentTransformerInterface $invoiceTransformer,
        FactoryInterface $orderInvoiceFactory,
        OrderDocumentRepositoryInterface $orderInvoiceRepository,
        StateMachineApplier $stateMachineApplier
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->invoiceTransformer = $invoiceTransformer;
        $this->orderInvoiceFactory = $orderInvoiceFactory;
        $this->orderInvoiceRepository = $orderInvoiceRepository;
        $this->stateMachineApplier = $stateMachineApplier;
    }

    /**
     * @Given /^I create a invoice for (my order)$/
     * @Given /^I create another invoice for (my order)$/
     */
    public function iCreateAInvoiceForOrder(OrderInterface $order)
    {
        $orderItem = reset($order->getItems());

        $orderInvoice = $this->orderInvoiceFactory->createNew();
        $orderInvoice = $this->invoiceTransformer->transform($order, $orderInvoice, [
            [
                'orderItemId' => $orderItem->getId(),
                'quantity' => 1,
            ],
        ]);

        $this->sharedStorage->set('orderInvoice', $orderInvoice);
    }

    /**
     * @Given /^I apply invoice transition "([^"]+)" to (latest order invoice)$/
     */
    public function iApplyInvoiceTransitionToInvoice($invoiceTransition, OrderInvoiceInterface $invoice)
    {
        $this->stateMachineApplier->apply($invoice, InvoiceTransitions::IDENTIFIER, $invoiceTransition);
    }
}

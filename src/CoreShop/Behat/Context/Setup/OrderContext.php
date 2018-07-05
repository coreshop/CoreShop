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
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\OrderInvoiceTransitions;
use CoreShop\Component\Order\OrderShipmentTransitions;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\PaymentTransitions;
use CoreShop\Component\Payment\Repository\PaymentRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;

final class OrderContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var ProposalTransformerInterface
     */
    private $orderTransformer;

    /**
     * @var FactoryInterface
     */
    private $orderFactory;

    /**
     * @var PaymentRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @var StateMachineApplier
     */
    private $stateMachineApplier;

    /**
     * @param SharedStorageInterface       $sharedStorage
     * @param StoreContextInterface        $storeContext
     * @param ProposalTransformerInterface $orderTransformer
     * @param FactoryInterface             $orderFactory
     * @param PaymentRepositoryInterface   $paymentRepository
     * @param StateMachineApplier          $stateMachineApplier
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        StoreContextInterface $storeContext,
        ProposalTransformerInterface $orderTransformer,
        FactoryInterface $orderFactory,
        PaymentRepositoryInterface $paymentRepository,
        StateMachineApplier $stateMachineApplier
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->storeContext = $storeContext;
        $this->orderTransformer = $orderTransformer;
        $this->orderFactory = $orderFactory;
        $this->paymentRepository = $paymentRepository;
        $this->stateMachineApplier = $stateMachineApplier;
    }

    /**
     * @Given /^I create an order from (my cart)$/
     */
    public function transformCartToOrder(CartInterface $cart)
    {
        $cart->setStore($this->storeContext->getStore());

        $order = $this->orderFactory->createNew();

        $order = $this->orderTransformer->transform($cart, $order);

        $this->sharedStorage->set('order', $order);
    }

    /**
     * @Given /^I apply payment transition "([^"]+)" to (latest order payment)$/
     */
    public function iApplyPaymentStateToLatestOrderPayment($paymentTransition, PaymentInterface $payment)
    {
        $this->stateMachineApplier->apply($payment, PaymentTransitions::IDENTIFIER, $paymentTransition);
    }

    /**
     * @Given /^I apply transition "([^"]+)" to (my order)$/
     */
    public function iApplyTransitionToOrder($transition, OrderInterface $order)
    {
        $this->stateMachineApplier->apply($order, OrderTransitions::IDENTIFIER, $transition);
    }

    /**
     * @Given /^I apply order invoice transition "([^"]+)" to (my order)$/
     */
    public function iApplyTransitionToOrderInvoice($transition, OrderInterface $order)
    {
        $this->stateMachineApplier->apply($order, OrderInvoiceTransitions::IDENTIFIER, $transition);
    }

    /**
     * @Given /^I apply order shipment transition "([^"]+)" to (my order)$/
     */
    public function iApplyTransitionToOrderShipment($transition, OrderInterface $order)
    {
        $this->stateMachineApplier->apply($order, OrderShipmentTransitions::IDENTIFIER, $transition);
    }
}

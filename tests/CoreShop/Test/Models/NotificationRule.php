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

namespace CoreShop\Test\Models;

use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Action\OrderMailConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\InvoiceStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\OrderPaymentStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\OrderStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\ShipmentStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CarriersConfigurationType;
use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleActionType;
use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleConditionType;
use CoreShop\Bundle\NotificationBundle\Form\Type\Rule\Action\MailActionConfigurationType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Order\InvoiceStates;
use CoreShop\Component\Order\InvoiceTransitions;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\OrderInvoiceStates;
use CoreShop\Component\Order\OrderPaymentStates;
use CoreShop\Component\Order\OrderShipmentStates;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\ShipmentStates;
use CoreShop\Component\Order\ShipmentTransitions;
use CoreShop\Test\Data;
use CoreShop\Test\RuleTest;

class NotificationRule extends RuleTest
{
    /**
     * @var CartInterface
     */
    protected $cart;

    /**
     * @var AddressInterface
     */
    protected $address;

    /**
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormRegistryName()
    {
        return 'coreshop.form_registry.notification_rule.conditions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionValidatorName()
    {
        return 'coreshop.notification_rule.validation.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormClass()
    {
        return NotificationRuleConditionType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormRegistryName()
    {
        return 'coreshop.form_registry.notification_rule.actions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionProcessorName()
    {
        return 'coreshop.notification_rule.applier';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormClass()
    {
        return NotificationRuleActionType::class;
    }

    /**
     * @return NotificationRuleInterface
     */
    protected function createRule()
    {
        /**
         * @var NotificationRuleInterface
         */
        $notificationRule = $this->getFactory('notification_rule')->createNew();
        $notificationRule->setName('test-rule');

        return $notificationRule;
    }

    /**
     * @return OrderInterface
     */
    private function createOrder()
    {
        $cart = Data::createCartWithProducts();
        /**
         * @var OrderInterface
         */
        $order = $this->getFactory('order')->createNew();
        $order = $this->get('coreshop.order.transformer.cart_to_order')->transform($cart, $order);

        return $order;
    }

    /**
     * @return OrderInvoiceInterface
     */
    private function createOrderInvoice()
    {
        $order = $this->createOrder();

        $processableItems = $this->get('coreshop.order.invoice.processable')->getProcessableItems($order);

        /**
         * @var OrderInvoiceInterface
         */
        $invoice = $this->getFactory('order_invoice')->createNew();
        $invoice = $this->get('coreshop.order.transformer.order_to_invoice')->transform($order, $invoice, $processableItems);

        $workflow = $this->get('coreshop.state_machine_manager')->get($invoice, InvoiceStates::IDENTIFIER);
        $workflow->apply($invoice, InvoiceTransitions::TRANSITION_CREATE);
        $workflow->apply($invoice, InvoiceTransitions::TRANSITION_COMPLETE);

        return $invoice;
    }

    /**
     * @return OrderShipmentInterface
     */
    private function createOrderShipment()
    {
        $order = $this->createOrder();

        $processableItems = $this->get('coreshop.order.shipment.processable')->getProcessableItems($order);

        /**
         * @var OrderShipmentInterface
         */
        $shipment = $this->getFactory('order_shipment')->createNew();
        $shipment = $this->get('coreshop.order.transformer.order_to_shipment')->transform($order, $shipment, $processableItems);

        $workflow = $this->get('coreshop.state_machine_manager')->get($shipment, ShipmentStates::IDENTIFIER);
        $workflow->apply($shipment, ShipmentTransitions::TRANSITION_CREATE);
        $workflow->apply($shipment, ShipmentTransitions::TRANSITION_SHIP);


        return $shipment;
    }

    /**
     * Test Rule Condition Invoice State.
     */
    public function testNotificationRuleOrderInvoiceStatePartial()
    {
        $this->printTestName();
        $this->assertConditionForm(InvoiceStateConfigurationType::class, 'order.invoiceState');

        $condition = $this->createConditionWithForm('order.invoiceState', [
            'invoiceState' => OrderInvoiceStates::STATE_PARTIALLY_INVOICED,
        ]);

        $invoice = $this->createOrderInvoice();

        $this->assertRuleCondition(['subject' => $invoice->getOrder(), 'params' => []], $condition, false);
    }

    /**
     * Test Rule Condition Invoice State.
     */
    public function testNotificationRuleOrderInvoiceStateFull()
    {
        $this->printTestName();
        $this->assertConditionForm(InvoiceStateConfigurationType::class, 'order.invoiceState');

        $condition = $this->createConditionWithForm('order.invoiceState', [
            'invoiceState' => OrderInvoiceStates::STATE_INVOICED,
        ]);

        $invoice = $this->createOrderInvoice();

        $this->assertRuleCondition(['subject' => $invoice->getOrder(), 'params' => []], $condition);
    }

    /**
     * Test Rule Condition Invoice State.
     */
    public function testNotificationRuleOrderShipmentStatePartial()
    {
        $this->printTestName();
        $this->assertConditionForm(ShipmentStateConfigurationType::class, 'order.shipmentState');

        $condition = $this->createConditionWithForm('order.shipmentState', [
            'shipmentState' => OrderShipmentStates::STATE_PARTIALLY_SHIPPED,
        ]);

        $shipment = $this->createOrderShipment();

        $this->assertRuleCondition(['subject' => $shipment->getOrder(), 'params' => []], $condition, false);
    }

    /**
     * Test Rule Condition Invoice State.
     */
    public function testNotificationRuleOrderShipmentStateFull()
    {
        $this->printTestName();
        $this->assertConditionForm(ShipmentStateConfigurationType::class, 'order.shipmentState');

        $condition = $this->createConditionWithForm('order.shipmentState', [
            'shipmentState' => OrderShipmentStates::STATE_SHIPPED,
        ]);

        $shipment = $this->createOrderShipment();

        $this->assertRuleCondition(['subject' => $shipment->getOrder(), 'params' => []], $condition);
    }

    /**
     * Test Rule Condition Order.
     */
    public function testNotificationRuleOrderOrderState()
    {
        $this->printTestName();
        $this->assertConditionForm(OrderStateConfigurationType::class, 'order.orderState');

        $condition = $this->createConditionWithForm('order.orderState', [
            'orderState' => OrderStates::STATE_NEW
        ]);

        $this->assertRuleCondition(['subject' => $this->createOrder(), 'params' => []], $condition);
    }

    /**
     * Test Rule Condition Carrier.
     */
    public function testNotificationRuleOrderCarrier()
    {
        $this->printTestName();
        $this->assertConditionForm(CarriersConfigurationType::class, 'order.carriers');

        $condition = $this->createConditionWithForm('order.carriers', [
            'carriers' => [Data::$carrier1],
        ]);

        $order = $this->createOrder();
        $order->setCarrier(Data::$carrier1);

        $this->assertRuleCondition(['subject' => $order, 'params' => []], $condition);
    }

    /**
     * Test Rule Condition Payment.
     */
    public function testNotificationRuleOrderPayment()
    {
        $this->printTestName();
        $this->assertConditionForm(OrderPaymentStateConfigurationType::class, 'order.orderPaymentState');

        $condition = $this->createConditionWithForm('order.orderPaymentState', [
            'orderPaymentState' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
        ]);

        $order = $this->createOrder();

        $this->assertRuleCondition(['subject' => $order, 'params' => []], $condition);
    }

    /**
     * Test Rule Condition Payment.
     */
    /*public function testNotificationRulePaymentPayment()
    {
        $this->printTestName();
        $this->assertConditionForm(PaymentStateConfigurationType::class, 'payment.paymentState');

        $condition = $this->createConditionWithForm('payment.paymentState', [
            'paymentState' => PaymentInterface::PAYMENT_TYPE_PARTIAL,
        ]);

        $order = $this->createOrder();

        $payment = $this->getFactory('payment')->createNew();
        $payment->setCurrency($order->getCurrency());
        $payment->setOrderId($order->getId());
        $payment->setTotalAmount($order->getTotal(true));
        $payment->setState('new');
        $payment->setDatePayment(Carbon::now());

        $this->getEntityManager()->persist($payment);
        $this->getEntityManager()->flush();

        $this->assertRuleCondition(['subject' => $payment, 'params' => []], $condition);
    }*/

    /**
     * Test Rule Condition Payment.
     */
    public function testNotificationRuleActionMail()
    {
        $this->printTestName();
        $this->assertActionForm(MailActionConfigurationType::class, 'order.mail');
    }

    /**
     * Test Rule Condition Payment.
     */
    public function testNotificationRuleActionOrderMail()
    {
        $this->printTestName();
        $this->assertActionForm(OrderMailConfigurationType::class, 'order.orderMail');
    }
}

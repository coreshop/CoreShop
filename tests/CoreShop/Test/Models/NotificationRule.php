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

namespace CoreShop\Test\Models;

use Carbon\Carbon;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Action\OrderMailConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\InvoiceStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\OrderStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\PaymentStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\ShipmentStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CarriersConfigurationType;
use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleActionType;
use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleConditionType;
use CoreShop\Bundle\NotificationBundle\Form\Type\Rule\Action\MailActionConfigurationType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Notification\Rule\Condition\Order\InvoiceStateChecker;
use CoreShop\Component\Core\Notification\Rule\Condition\Order\OrderStateChecker;
use CoreShop\Component\Core\Notification\Rule\Condition\Order\PaymentStateChecker;
use CoreShop\Component\Core\Notification\Rule\Condition\Order\ShipmentStateChecker;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use CoreShop\Component\Order\Model\OrderShipmentInterface;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
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
     * Setup
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
         * @var $notificationRule NotificationRuleInterface
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
         * @var $order OrderInterface
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
         * @var $invoice OrderInvoiceInterface
         */
        $invoice = $this->getFactory('order_invoice')->createNew();
        $invoice = $this->get('coreshop.order.transformer.order_to_invoice')->transform($order, $invoice, $processableItems);

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
         * @var $shipment OrderShipmentInterface
         */
        $shipment = $this->getFactory('order_shipment')->createNew();
        $shipment = $this->get('coreshop.order.transformer.order_to_shipment')->transform($order, $shipment, $processableItems);

        return $shipment;
    }

    /**
     * Test Rule Condition Invoice State
     */
    public function testNotificationRuleOrderInvoiceStatePartial()
    {
        $this->printTestName();
        $this->assertConditionForm(InvoiceStateConfigurationType::class, 'order.invoiceState');

        $condition = $this->createConditionWithForm('order.invoiceState', [
            'invoiceState' => InvoiceStateChecker::INVOICE_TYPE_PARTIAL
        ]);

        $invoice = $this->createOrderInvoice();

        $this->assertRuleCondition(['subject' => $invoice->getOrder(), 'params' => []], $condition, false);
    }

    /**
     * Test Rule Condition Invoice State
     */
    public function testNotificationRuleOrderInvoiceStateFull()
    {
        $this->printTestName();
        $this->assertConditionForm(InvoiceStateConfigurationType::class, 'order.invoiceState');

        $condition = $this->createConditionWithForm('order.invoiceState', [
            'invoiceState' => InvoiceStateChecker::INVOICE_TYPE_FULL
        ]);

        $invoice = $this->createOrderInvoice();

        $this->assertRuleCondition(['subject' => $invoice->getOrder(), 'params' => []], $condition);
    }

    /**
     * Test Rule Condition Invoice State
     */
    public function testNotificationRuleOrderShipmentStatePartial()
    {
        $this->printTestName();
        $this->assertConditionForm(ShipmentStateConfigurationType::class, 'order.shipmentState');

        $condition = $this->createConditionWithForm('order.shipmentState', [
            'shipmentState' => ShipmentStateChecker::SHIPMENT_TYPE_PARTIAL
        ]);

        $shipment = $this->createOrderShipment();

        $this->assertRuleCondition(['subject' => $shipment->getOrder(), 'params' => []], $condition, false);
    }

    /**
     * Test Rule Condition Invoice State
     */
    public function testNotificationRuleOrderShipmentStateFull()
    {
        $this->printTestName();
        $this->assertConditionForm(ShipmentStateConfigurationType::class, 'order.shipmentState');

        $condition = $this->createConditionWithForm('order.shipmentState', [
            'shipmentState' => ShipmentStateChecker::SHIPMENT_TYPE_FULL
        ]);

        $shipment = $this->createOrderShipment();

        $this->assertRuleCondition(['subject' => $shipment->getOrder(), 'params' => []], $condition);
    }

     /**
     * Test Rule Condition Order
     */
    public function testNotificationRuleOrderOrderState()
    {
        $this->printTestName();
        $this->assertConditionForm(OrderStateConfigurationType::class, 'order.orderState');

        $condition = $this->createConditionWithForm('order.orderState', [
            'transitionType' => OrderStateChecker::TRANSITION_FROM,
            'states' => [
                WorkflowManagerInterface::ORDER_STATUS_INITIALIZED
            ]
        ]);

        $this->assertRuleCondition(['subject' => $this->createOrder(), 'params' => [
            'fromState' => WorkflowManagerInterface::ORDER_STATUS_INITIALIZED,
            'toState' => WorkflowManagerInterface::ORDER_STATUS_PENDING_PAYMENT
        ]], $condition);
    }

    /**
     * Test Rule Condition Carrier
     */
    public function testNotificationRuleOrderCarrier()
    {
        $this->printTestName();
        $this->assertConditionForm(CarriersConfigurationType::class, 'order.carriers');

        $condition = $this->createConditionWithForm('order.carriers', [
            'carriers' => [Data::$carrier1]
        ]);

        $order = $this->createOrder();
        $order->setCarrier(Data::$carrier1);

        $this->assertRuleCondition(['subject' => $order, 'params' => []], $condition);
    }

    /**
     * Test Rule Condition Payment
     */
    public function testNotificationRuleOrderPayment()
    {
        $this->printTestName();
        $this->assertConditionForm(PaymentStateConfigurationType::class, 'order.paymentState');

        $condition = $this->createConditionWithForm('order.paymentState', [
            'paymentState' => PaymentStateChecker::PAYMENT_TYPE_PARTIAL
        ]);

        $order = $this->createOrder();

        $this->assertRuleCondition(['subject' => $order, 'params' => []], $condition, false);
    }

    /**
     * Test Rule Condition Invoice State
     */
    public function testNotificationRuleInvoiceInvoiceStatePartial()
    {
        $this->printTestName();
        $this->assertConditionForm(InvoiceStateConfigurationType::class, 'invoice.invoiceState');

        $condition = $this->createConditionWithForm('invoice.invoiceState', [
            'invoiceState' => InvoiceStateChecker::INVOICE_TYPE_PARTIAL
        ]);

        $invoice = $this->createOrderInvoice();

        $this->assertRuleCondition(['subject' => $invoice, 'params' => []], $condition, false);
    }

    /**
     * Test Rule Condition Invoice State
     */
    public function testNotificationRuleInvoiceInvoiceStateFull()
    {
        $this->printTestName();
        $this->assertConditionForm(InvoiceStateConfigurationType::class, 'invoice.invoiceState');

        $condition = $this->createConditionWithForm('invoice.invoiceState', [
            'invoiceState' => InvoiceStateChecker::INVOICE_TYPE_FULL
        ]);

        $invoice = $this->createOrderInvoice();

        $this->assertRuleCondition(['subject' => $invoice, 'params' => []], $condition);
    }

     /**
     * Test Rule Condition Invoice State
     */
    public function testNotificationRuleShipmentShipmentStatePartial()
    {
        $this->printTestName();
        $this->assertConditionForm(ShipmentStateConfigurationType::class, 'shipment.shipmentState');

        $condition = $this->createConditionWithForm('shipment.shipmentState', [
            'shipmentState' => ShipmentStateChecker::SHIPMENT_TYPE_PARTIAL
        ]);

        $shipment = $this->createOrderShipment();

        $this->assertRuleCondition(['subject' => $shipment, 'params' => []], $condition, false);
    }

    /**
     * Test Rule Condition Invoice State
     */
    public function testNotificationRuleShipmentShipmentStateFull()
    {
        $this->printTestName();
        $this->assertConditionForm(ShipmentStateConfigurationType::class, 'shipment.shipmentState');

        $condition = $this->createConditionWithForm('shipment.shipmentState', [
            'shipmentState' => ShipmentStateChecker::SHIPMENT_TYPE_FULL
        ]);

        $shipment = $this->createOrderShipment();

        $this->assertRuleCondition(['subject' => $shipment, 'params' => []], $condition);
    }

    /**
     * Test Rule Condition Payment
     */
    public function testNotificationRulePaymentPayment()
    {
        $this->printTestName();
        $this->assertConditionForm(PaymentStateConfigurationType::class, 'payment.paymentState');

        $condition = $this->createConditionWithForm('payment.paymentState', [
            'paymentState' => PaymentStateChecker::PAYMENT_TYPE_PARTIAL
        ]);

        $order = $this->createOrder();

        /**
         * @var $payment PaymentInterface
         */
        $payment = $this->getFactory('payment')->createNew();
        $payment->setCurrency($order->getCurrency());
        $payment->setOrderId($order->getId());
        $payment->setTotalAmount($order->getTotal(true));
        $payment->setState('new');
        $payment->setDatePayment(Carbon::now());

        $this->getEntityManager()->persist($payment);
        $this->getEntityManager()->flush();

        $this->assertRuleCondition(['subject' => $payment, 'params' => []], $condition);
    }

    /**
     * Test Rule Condition Payment
     */
    public function testNotificationRuleActionMail()
    {
        $this->printTestName();
        $this->assertActionForm(MailActionConfigurationType::class, 'order.mail');
    }

    /**
     * Test Rule Condition Payment
     */
    public function testNotificationRuleActionOrderMail()
    {
        $this->printTestName();
        $this->assertActionForm(OrderMailConfigurationType::class, 'order.orderMail');
    }
}

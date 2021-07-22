<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Action\OrderMailConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\InvoiceStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\OrderInvoiceStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\OrderPaymentStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\OrderShippingStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\OrderStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\ShipmentStateConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition\StateTransitionConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CarriersConfigurationType;
use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleActionType;
use CoreShop\Bundle\NotificationBundle\Form\Type\NotificationRuleConditionType;
use CoreShop\Bundle\NotificationBundle\Form\Type\Rule\Action\MailActionConfigurationType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Notification\Rule\Condition\SimpleStateChecker;
use CoreShop\Component\Core\Notification\Rule\Condition\StateTransitionChecker;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Order\InvoiceStates;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Core\Model\OrderShipmentInterface;
use CoreShop\Component\Order\OrderInvoiceStates;
use CoreShop\Component\Order\OrderPaymentStates;
use CoreShop\Component\Order\OrderShipmentStates;
use CoreShop\Component\Order\OrderShipmentTransitions;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\ShipmentStates;
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
         * @var $notificationRule NotificationRuleInterface
         */
        $notificationRule = $this->getFactory('notification_rule')->createNew();
        $notificationRule->setName('test-rule');
        $notificationRule->setActive(true);

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
     * Test Simple State Checker
     */
    public function testSimpleStateChecker()
    {
        $orderShipment = $this->get('coreshop.factory.order_shipment')->createNew();
        $orderShipment->setState(OrderShipmentStates::STATE_SHIPPED);

        $simpleStateChecker = new SimpleStateChecker(OrderShipmentInterface::class, 'state', 'state');

        $this->assertTrue($simpleStateChecker->isNotificationRuleValid($orderShipment, [], ['state' => OrderShipmentStates::STATE_SHIPPED]));
    }

    /**
     * Test Transition Checker
     */
    public function testTransitionChecker()
    {
        $mock = $this->createMock(OrderShipment::class);

        $simpleStateChecker = new StateTransitionChecker(get_class($mock), OrderShipmentTransitions::IDENTIFIER);

        $this->assertTrue($simpleStateChecker->isNotificationRuleValid($mock,
            [
                'transition' => OrderShipmentTransitions::TRANSITION_SHIP,
                'workflow' => OrderShipmentTransitions::IDENTIFIER
            ], [
                'transition' => OrderShipmentTransitions::TRANSITION_SHIP
            ]
        ));
    }

    /**
     * Test Rule Condition Invoice State.
     */
    public function testNotificationRuleOrderTransitionForm()
    {
        $this->printTestName();
        $this->assertConditionForm(StateTransitionConfigurationType::class, 'order.orderTransition');
        $this->assertConditionForm(StateTransitionConfigurationType::class, 'order.orderShippingTransition');
        $this->assertConditionForm(StateTransitionConfigurationType::class, 'order.orderInvoiceTransition');
        $this->assertConditionForm(StateTransitionConfigurationType::class, 'order.orderPaymentTransition');
        $this->assertConditionForm(StateTransitionConfigurationType::class, 'invoice.invoiceTransition');
        $this->assertConditionForm(StateTransitionConfigurationType::class, 'shipment.shipmentTransition');
        $this->assertConditionForm(StateTransitionConfigurationType::class, 'payment.paymentTransition');
    }


    /**
     * Test Rule Condition Invoice State.
     */
    public function testNotificationRuleOrderInvoiceStateForm()
    {
        $this->printTestName();
        $this->assertConditionForm(OrderInvoiceStateConfigurationType::class, 'order.orderInvoiceState');

        $this->createConditionWithForm('order.orderInvoiceState', [
            'orderInvoiceState' => OrderInvoiceStates::STATE_PARTIALLY_INVOICED,
        ]);
    }

    /**
     * Test Rule Condition Invoice State.
     */
    public function testNotificationRuleOrderShipmentStateForm()
    {
        $this->printTestName();
        $this->assertConditionForm(OrderShippingStateConfigurationType::class, 'order.orderShippingState');

        $this->createConditionWithForm('order.orderShippingState', [
            'orderShippingState' => OrderShipmentStates::STATE_PARTIALLY_SHIPPED,
        ]);
    }

    /**
     * Test Rule Condition Order.
     */
    public function testNotificationRuleOrderOrderStateForm()
    {
        $this->printTestName();
        $this->assertConditionForm(OrderStateConfigurationType::class, 'order.orderState');

        $this->createConditionWithForm('order.orderState', [
            'orderState' => OrderStates::STATE_NEW
        ]);
    }

    /**
     * Test Rule Condition Carrier.
     */
    public function testNotificationRuleOrderCarrier()
    {
        $this->printTestName();
        $this->assertConditionForm(CarriersConfigurationType::class, 'order.carriers');

        $condition = $this->createConditionWithForm('order.carriers', [
            'carriers' => [Data::$carrier1->getId()],
        ]);

        $order = $this->createOrder();
        $order->setCarrier(Data::$carrier1);

        $this->assertRuleCondition($order, $condition, ['params' => []]);
    }

    /**
     * Test Rule Condition Payment.
     */
    public function testNotificationRuleOrderPaymentForm()
    {
        $this->printTestName();
        $this->assertConditionForm(OrderPaymentStateConfigurationType::class, 'order.orderPaymentState');

        $this->createConditionWithForm('order.orderPaymentState', [
            'orderPaymentState' => OrderPaymentStates::STATE_AWAITING_PAYMENT,
        ]);
    }

    /**
     * Test Rule Condition Invoice State.
     */
    public function testNotificationRuleInvoiceInvoiceStatForm()
    {
        $this->printTestName();
        $this->assertConditionForm(InvoiceStateConfigurationType::class, 'invoice.invoiceState');

        $this->createConditionWithForm('invoice.invoiceState', [
            'invoiceState' => InvoiceStates::STATE_CANCELLED
        ]);
    }

    /**
     * Test Rule Condition Invoice State.
     */
    public function testNotificationRuleShipmentShipmentStateForm()
    {
        $this->printTestName();
        $this->assertConditionForm(ShipmentStateConfigurationType::class, 'shipment.shipmentState');

        $this->createConditionWithForm('shipment.shipmentState', [
            'shipmentState' => ShipmentStates::STATE_SHIPPED,
        ]);
    }

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

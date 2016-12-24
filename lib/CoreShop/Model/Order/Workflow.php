<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\Model\Order;
use Pimcore\Model\Document;
use Pimcore\Model\Object;
use CoreShop\Model\Configuration;
use CoreShop\Model\Order;
use CoreShop\Mail;
/**
 * Class Workflow
 * @package CoreShop\Model\Order
 */
class Workflow
{
    /**
     * Fires before a action event triggers.
     *
     * Checks if:
     *  - last state is same as new: abort (implemented)
     *
     * @param \Zend_EventManager_Event $event
     *
     * @throws \Exception
     */
    public static function beforeDispatchOrderChange($event)
    {
        /** @var \Pimcore\WorkflowManagement\Workflow\Manager $manager */
        $manager = $event->getTarget();
        $data = $event->getParam('data');
        $currentStatus = $manager->getWorkflowStateForElement()->getStatus();
        $newStatus = $data['newStatus'];
        $orderObject = $manager->getElement();
        if ($orderObject instanceof Order) {
            if ($currentStatus === $newStatus) {
                throw new \Exception('Cannot apply same orderState again. (' . $currentStatus . ' => ' . $newStatus .')');
            }
        }
    }
    /**
     * @param \Zend_EventManager_Event $event
     */
    public static function dispatchOrderChangeFailed($event)
    {
        $exception = $event->getParam('exception');
        \Pimcore\Logger::err('CoreShop Workflow OrderChange failed. Reason: ' . $exception->getMessage());
    }
    /**
     * @param \Zend_EventManager_Event $event
     */
    public static function dispatchOrderChange($event)
    {
        $manager = $event->getTarget();
        $data = $event->getParam('data');
        $additional = $data['additional'];
        $orderObject = $manager->getElement();
        $oldStatus = $data['oldStatus'];
        $newStatus = $data['newStatus'];
        if ($orderObject instanceof Order) {
            //create invoice, if allowed.
            if (self::checkAutomatedInvoicePossibility($orderObject, $oldStatus, $newStatus)) {
                $orderObject->createInvoiceForAllItems();
            }
            //send confirmation order mail.
            if (isset($additional[Order\State::ORDER_STATE_CONFIRMATION_MAIL]) && $additional[Order\State::ORDER_STATE_CONFIRMATION_MAIL] === 'yes') {
                $confirmationMailPath = Configuration::get('SYSTEM.MAIL.ORDER.STATES.CONFIRMATION.' . strtoupper($orderObject->getLang()));
                $emailDocument = Document::getById($confirmationMailPath);
                if ($emailDocument instanceof Document\Email) {
                    Mail::sendOrderMail($emailDocument, $orderObject);
                }
            }
            //send order update status mail.
            if (isset($additional[Order\State::ORDER_STATE_STATUS_MAIL]) && $additional[Order\State::ORDER_STATE_STATUS_MAIL] === 'yes') {
                $updateMailPath = Configuration::get('SYSTEM.MAIL.ORDER.STATES.UPDATE.' . strtoupper($orderObject->getLang()));
                $emailDocument = Document::getById($updateMailPath);
                if ($emailDocument instanceof Document\Email) {
                    Mail::sendOrderMail($emailDocument, $orderObject);
                }
            }
        }
    }
    /**
     * @param Order $order
     * @param $oldStatus
     * @param $newStatus
     * @return bool
     */
    private static function checkAutomatedInvoicePossibility(Order $order, $oldStatus, $newStatus)
    {
        if ((bool) Configuration::get('SYSTEM.INVOICE.CREATE') === false) {
            return false;
        }
        $allowedStatuses = [Order\State::STATUS_PENDING_PAYMENT, Order\State::STATUS_PAYMENT_REVIEW];
        if (!in_array($oldStatus, $allowedStatuses)) {
            return false;
        }
        $invoices = $order->getInvoices();
        if (count($invoices) !== 0) {
            return false;
        }
        return true;
    }

    public static function getWorkflowConfig()
    {
        return [
            "name" => "OrderState",
            "id" => NULL,
            "workflowSubject" => [
                "types" => ["object"],
                "classes" => [8],
            ],
            "enabled" => true,
            "defaultState" => "new",
            "defaultStatus" => "pending",
            "allowUnpublished" => true,
            "states" => [
                [
                    "name" => "new",
                    "label" => "Pending",
                    "color" => "#9bc4c4"
                ],
                [
                    "name" => "pending_payment",
                    "label" => "Pending Payment",
                    "color" => "#d0c31f"
                ],
                [
                    "name" => "processing",
                    "label" => "Processing",
                    "color" => "#3081ba"
                ],
                [
                    "name" => "complete",
                    "label" => "Complete",
                    "color" => "#73a623"
                ],
                [
                    "name" => "closed",
                    "label" => "Closed",
                    "color" => "#ffc301"
                ],
                [
                    "name" => "canceled",
                    "label" => "Canceled",
                    "color" => "#c12f30"
                ],
                [
                    "name" => "holded",
                    "label" => "On Hold",
                    "color" => "#b9c1bd"
                ],
                [
                    "name" => "payment_review",
                    "label" => "Payment Review",
                    "color" => "#ae61db"
                ]
            ],
            "statuses" => [
                [
                    "name" => "pending",
                    "label" => "Pending (Ausstehend)",
                    "elementPublished" => true
                ],
                [
                    "name" => "pending_payment",
                    "label" => "Pending Payment (Ausstehende Zahlung)",
                    "elementPublished" => true
                ],
                [
                    "name" => "processing",
                    "label" => "Processing (Verarbeitung)",
                    "elementPublished" => true
                ],
                [
                    "name" => "complete",
                    "label" => "Complete (Vollständig)",
                    "elementPublished" => true
                ],
                [
                    "name" => "closed",
                    "label" => "Closed (Geschlossen)",
                    "elementPublished" => true
                ],
                [
                    "name" => "canceled",
                    "label" => "Canceled (Storniert)",
                    "elementPublished" => true
                ],
                [
                    "name" => "holded",
                    "label" => "On Hold (Zurückgestellt)",
                    "elementPublished" => true
                ],
                [
                    "name" => "payment_review",
                    "label" => "Payment Review (Zahlungsprüfung)",
                    "elementPublished" => true
                ]
            ],
            "actions" => [
                [
                    "name" => "change_order_state",
                    "label" => "Change Order State",
                    "transitionTo" => [
                        "new" => [
                            "pending"
                        ],
                        "pending_payment" => [
                            "pending_payment"
                        ],
                        "processing" => [
                            "processing"
                        ],
                        "complete" => [
                            "complete"
                        ],
                        "closed" => [
                            "closed"
                        ],
                        "canceled" => [
                            "canceled"
                        ],
                        "holded" => [
                            "holded"
                        ],
                        "payment_review" => [
                            "payment_review"
                        ]
                    ],
                    "events" => [
                        "before" => ["\\CoreShop\\Model\\Order\\Workflow", "beforeDispatchOrderChange"],
                        "success" => ["\\CoreShop\\Model\\Order\\Workflow", "dispatchOrderChange"],
                        "failure" => ["\\CoreShop\\Model\\Order\\Workflow", "dispatchOrderChangeFailed"]
                    ],
                    "notes" => [
                        "type" => "Order State Change",
                        "required" => false
                    ],
                    "additionalFields" => [
                        [
                            "name"      => "sendOrderConfirmationMail",
                            "fieldType" => "select",
                            "title"     => "Send Order Confirmation Mail",
                            "defaultValue" => "no",
                            "options"   => [["key" => "Yes", "value" => "yes"], ["key" => "No", "value" => "no"]]
                        ],
                        [
                            "name"      => "sendOrderStatusMail",
                            "fieldType" => "select",
                            "title"     => "Send Order Status Update Mail",
                            "defaultValue" => "no",
                            "options"   => [["key" => "Yes", "value" => "yes"], ["key" => "No", "value" => "no"]]
                        ]
                    ]
                ]
            ],
            "transitionDefinitions" => [
                "pending" => [
                    "validActions" => [
                        "change_order_state" => null,
                    ]
                ],
                "pending_payment" => [
                    "validActions" => [
                        "change_order_state" => null,
                    ]
                ],
                "processing" => [
                    "validActions" => [
                        "change_order_state" => null,
                    ]
                ],
                "complete" => [
                    "validActions" => [
                        "change_order_state" => null,
                    ]
                ],
                "closed" => [
                    "validActions" => [
                        "change_order_state" => null,
                    ]
                ],
                "canceled" => [
                    "validActions" => [
                        "change_order_state" => null,
                    ]
                ],
                "holded" => [
                    "validActions" => [
                        "change_order_state" => null,
                    ]
                ],
                "payment_review" => [
                    "validActions" => [
                        "change_order_state" => null,
                    ]
                ],
            ]
        ];
    }
}
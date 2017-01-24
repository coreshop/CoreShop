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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\Model\Order;

use CoreShop\Model\Mail\Rule;
use CoreShop\Model\Order;

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
        $currentState = $manager->getWorkflowStateForElement()->getState();
        $newState = $data['newState'];
        $orderObject = $manager->getElement();
        if ($orderObject instanceof Order) {
            if ($currentState === $newState) {
                throw new \Exception('Cannot apply same orderState again. (' . $currentState . ' => ' . $newState .')');
            } elseif (!self::newStateIsValid($orderObject, $currentState, $newState)) {
                throw new \Exception('New State is not valid.');
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
        $orderObject = $manager->getElement();

        $data = $event->getParam('data');
        /*$additional = $data['additional'];
        $oldStatus = $data['oldStatus'];
        $newStatus = $data['newStatus'];*/
        $oldState = $data['oldState'];
        $newState = $data['newState'];

        if ($orderObject instanceof Order) {
            Rule::apply('order', $orderObject, [
                'fromState' => $oldState,
                'toState' => $newState
            ]);
        }
    }

    /**
     * Check if new state is valid.
     *
     * @param Order $order
     * @param $currentState
     * @param $newState
     *
     * @return bool
     */
    private static function newStateIsValid($order, $currentState, $newState)
    {
        if ($currentState !== State::STATE_INITIALIZED && $newState === State::STATE_INITIALIZED) {
            return false;
        } elseif ($newState === State::STATE_COMPLETE) {
            if ($order->isFullyInvoiced() && $order->isFullyShipped()) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Get Workflow Object for Pimcore >= 4.4.2
     *
     * @return \Pimcore\Model\Workflow
     */
    public static function getWorkflowObject() {
        $workflowObject = new \Pimcore\Model\Workflow();
        $workflowObject->setName("OrderState");
        $workflowObject->setWorkflowSubject([
            "types" => ["object"],
            "classes" => [Order::classId()]
        ]);
        $workflowObject->setDefaultState("initialized");
        $workflowObject->setDefaultStatus("initialized");
        $workflowObject->setAllowUnpublished(true);
        $workflowObject->setStates([
            [
                "name" => "initialized",
                "label" => "Initialized",
                "color" => "#4d4a4c"
            ],
            [
                "name" => "new",
                "label" => "New",
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
        ]);
        $workflowObject->setStatuses([
            [
                "name" => "initialized",
                "label" => "Initialized",
                "elementPublished" => true
            ],
            [
                "name" => "pending",
                "label" => "Pending",
                "elementPublished" => true
            ],
            [
                "name" => "pending_payment",
                "label" => "Pending Payment",
                "elementPublished" => true
            ],
            [
                "name" => "processing",
                "label" => "Processing",
                "elementPublished" => true
            ],
            [
                "name" => "complete",
                "label" => "Complete",
                "elementPublished" => true
            ],
            [
                "name" => "closed",
                "label" => "Closed",
                "elementPublished" => true
            ],
            [
                "name" => "canceled",
                "label" => "Canceled",
                "elementPublished" => true
            ],
            [
                "name" => "holded",
                "label" => "On Hold",
                "elementPublished" => true
            ],
            [
                "name" => "payment_review",
                "label" => "Payment Review",
                "elementPublished" => true
            ]
        ]);
        $workflowObject->setActions([
            [
                "name" => "change_order_state",
                "label" => "Change Order State",
                "transitionTo" => [
                    "initialized" => [
                        "initialized"
                    ],
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
                ]
            ]
        ]);
        $workflowObject->setTransitionDefinitions([
            "initialized" => [
                "validActions" => [
                    "change_order_state" => null
                ]
            ],
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
        ]);

        return $workflowObject;
    }

    /**
     * @deprecated will be removed with CoreShop 1.3
     * @return array
     */
    public static function getWorkflowConfig()
    {
        return [
            "name" => "OrderState",
            "id" => null,
            "workflowSubject" => [
                "types" => ["object"],
                "classes" => [Order::classId()],
            ],
            "enabled" => true,
            "defaultState" => "initialized",
            "defaultStatus" => "initialized",
            "allowUnpublished" => true,
            "states" => [
                [
                    "name" => "initialized",
                    "label" => "Initialized",
                    "color" => "#4d4a4c"
                ],
                [
                    "name" => "new",
                    "label" => "New",
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
                    "name" => "initialized",
                    "label" => "Initialized",
                    "elementPublished" => true
                ],
                [
                    "name" => "pending",
                    "label" => "Pending",
                    "elementPublished" => true
                ],
                [
                    "name" => "pending_payment",
                    "label" => "Pending Payment",
                    "elementPublished" => true
                ],
                [
                    "name" => "processing",
                    "label" => "Processing",
                    "elementPublished" => true
                ],
                [
                    "name" => "complete",
                    "label" => "Complete",
                    "elementPublished" => true
                ],
                [
                    "name" => "closed",
                    "label" => "Closed",
                    "elementPublished" => true
                ],
                [
                    "name" => "canceled",
                    "label" => "Canceled",
                    "elementPublished" => true
                ],
                [
                    "name" => "holded",
                    "label" => "On Hold",
                    "elementPublished" => true
                ],
                [
                    "name" => "payment_review",
                    "label" => "Payment Review",
                    "elementPublished" => true
                ]
            ],
            "actions" => [
                [
                    "name" => "change_order_state",
                    "label" => "Change Order State",
                    "transitionTo" => [
                        "initialized" => [
                            "initialized"
                        ],
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
                    ]
                ]
            ],
            "transitionDefinitions" => [
                "initialized" => [
                    "validActions" => [
                        "change_order_state" => null
                    ]
                ],
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

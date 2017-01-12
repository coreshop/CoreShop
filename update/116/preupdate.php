<?php

$db = \Pimcore\Db::get();

//install workflow data!!
$orderListing = \CoreShop\Model\Order::getList();
$classId = $orderListing->getClassId();

$workflowConfig = [
    "name" => "OrderState",
    "id" => null,
    "workflowSubject" => [
        "types" => ["object"],
        "classes" => [8],
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

$systemWorkflowConfig = \Pimcore\WorkflowManagement\Workflow\Config::getWorkflowManagementConfig(true);

$configFile = PIMCORE_CONFIGURATION_DIRECTORY . '/workflowmanagement.php';

//set defaults
$workflowConfig['workflowSubject']['classes'] = [$classId];

//no workflow file. create it!
if ($systemWorkflowConfig === null) {

    //set defaults
    $workflowConfig['id'] = 1;

    $workflowCompleteData = [
        'workflows' => [ $workflowConfig ]
    ];

    \Pimcore\File::putPhpFile($configFile, to_php_data_file_format($workflowCompleteData));
} else {
    $hasCoreShopWorkflow = false;
    $lastId = 1;

    if (isset($systemWorkflowConfig['workflows']) && is_array($systemWorkflowConfig['workflows'])) {
        foreach ($systemWorkflowConfig['workflows'] as $workflow) {
            if ($workflow['name'] === 'OrderState') {
                $hasCoreShopWorkflow = true;
                break;
            }
            $lastId = (int) $workflow['id'];
        }

        if ($hasCoreShopWorkflow === false) {
            //set defaults
            $workflowConfig['id'] = $lastId+1;
            $systemWorkflowConfig['workflows'] = array_merge($systemWorkflowConfig['workflows'], [$workflowConfig]);
            \Pimcore\File::putPhpFile($configFile, to_php_data_file_format($systemWorkflowConfig));
        }
    }
}

//transfer orderStates to workflow
$orders = \CoreShop\Model\Order::getList();

$data = [];
if (is_array($orders->load())) {
    /** @var \CoreShop\Model\Order $order */
    foreach ($orders->load() as $order) {
        /** @var \CoreShop\Model\Order\State $order */
        $currentState = $order->getOrderState();
        if ($currentState instanceof \CoreShop\Model\Order\State) {
            $identifier = $currentState->getIdentifier();

            $newState = 'complete';
            $newStatus = 'complete';

            switch ($identifier) {
                case 'QUEUE':
                    $newState = 'new';
                    $newStatus = 'pending';
                    break;
                case 'PAYMENT':
                    $newState = 'processing';
                    $newStatus = 'processing';
                    break;
                case 'PREPERATION':
                    $newState = 'processing';
                    $newStatus = 'processing';
                    break;
                case 'SHIPPING':
                    $newState = 'processing';
                    $newStatus = 'processing';
                    break;
                case 'DELIVERED':
                    $newState = 'complete';
                    $newStatus = 'complete';
                    break;
                case 'CANCELED':
                    $newState = 'canceled';
                    $newStatus = 'canceled';
                    break;
                case 'REFUND':
                    $newState = 'processing';
                    $newStatus = 'processing';
                    break;
                case 'ERROR':
                    $newState = 'canceled';
                    $newStatus = 'canceled';
                    break;
                case 'OUTOFSTOCK':
                    $newState = 'processing';
                    $newStatus = 'processing';
                    break;
                case 'BANKWIRE':
                    $newState = 'new';
                    $newStatus = 'pending';
                    break;
                case 'OUTOFSTOCK_UNPAID':
                    $newState = 'processing';
                    $newStatus = 'processing';
                    break;
                case 'COD':
                    $newState = 'new';
                    $newStatus = 'pending';
                    break;
            }

            $elementType = \Pimcore\Model\Element\Service::getElementType($order);
            $workflowState = new \Pimcore\Model\Element\WorkflowState();
            $workflowState->setCid($order->getId());
            $workflowState->setCtype($elementType);
            $workflowState->setWorkflowId($workflowConfig['id']);
            $workflowState->setState($newState);
            $workflowState->setStatus($newStatus);
            $workflowState->save();
        }
    }
}

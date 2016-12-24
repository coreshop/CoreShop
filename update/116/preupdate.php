<?php

$db = \Pimcore\Db::get();

//install workflow data!!
$orderListing = \CoreShop\Model\Order::getList();
$classId = $orderListing->getClassId();

$workflowConfig = \CoreShop\Model\Order\Workflow::getWorkflowConfig();
$systemWorkflowConfig = \Pimcore\WorkflowManagement\Workflow\Config::getWorkflowManagementConfig(true);

$configFile = PIMCORE_CONFIGURATION_DIRECTORY . '/workflowmanagement.php';

//set defaults
$workflowConfig['workflowSubject']['classes'] = [$classId];

//no workflow file. create it!
if($systemWorkflowConfig === NULL) {

    //set defaults
    $workflowConfig['id'] = 1;

    $workflowCompleteData = [
        'workflows' => [ $workflowConfig ]
    ];

    \Pimcore\File::putPhpFile($configFile, to_php_data_file_format($workflowCompleteData));

} else {

    $hasCoreShopWorkflow = FALSE;
    $lastId = 1;

    if(isset($systemWorkflowConfig['workflows']) && is_array($systemWorkflowConfig['workflows'])) {
        foreach($systemWorkflowConfig['workflows'] as $workflow) {
            if($workflow['name'] === 'OrderState') {
                $hasCoreShopWorkflow = TRUE;
                break;
            }
            $lastId = (int) $workflow['id'];
        }

        if($hasCoreShopWorkflow === FALSE) {
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
if (is_array($orders->getData())) {
    /** @var \CoreShop\Model\Order $order */
    foreach ($orders->getData() as $order) {
        /** @var \CoreShop\Model\Order\State $order */
        $currentState = $order->getOrderState();
        if( $currentState instanceof \CoreShop\Model\Order\State ) {
            $identifier = $currentState->getIdentifier();

            $newState = 'complete';
            $newStatus = 'complete';

            switch($identifier) {
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
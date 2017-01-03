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

use CoreShop\Model\Configuration;
use CoreShop\Model\Order;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Note;
use Pimcore\WorkflowManagement\Workflow;

/**
 * Class State
 * @package CoreShop\Model\Order
 */
class State
{
    const STATE_INITIALIZED        = 'initialized';
    const STATE_NEW                = 'new';
    const STATE_PENDING_PAYMENT    = 'pending_payment';
    const STATE_PROCESSING         = 'processing';
    const STATE_COMPLETE           = 'complete';
    const STATE_CLOSED             = 'closed';
    const STATE_CANCELED           = 'canceled';
    const STATE_HOLDED             = 'holded';
    const STATE_PAYMENT_REVIEW     = 'payment_review';

    const STATUS_INITIALIZED        = 'initialized';
    const STATUS_PENDING            = 'pending';
    const STATUS_PENDING_PAYMENT    = 'pending_payment';
    const STATUS_PROCESSING         = 'processing';
    const STATUS_COMPLETE           = 'complete';
    const STATUS_CLOSED             = 'closed';
    const STATUS_CANCELED           = 'canceled';
    const STATUS_HOLDED             = 'holded';
    const STATUS_PAYMENT_REVIEW     = 'payment_review';

    protected static $STATE_CONFIG = [

        self::STATE_INITIALIZED => [
            'color' => '#4d4a4c'
        ],
        self::STATE_NEW => [
            'color' => '#9bc4c4'
        ],
        self::STATE_PENDING_PAYMENT => [
            'color' => '#d0c31f'
        ],
        self::STATE_PROCESSING => [
            'color' => '#3081ba'
        ],
        self::STATE_COMPLETE => [
            'color' => '#73a623'
        ],
        self::STATE_CLOSED => [
            'color' => '#ffc301'
        ],
        self::STATE_CANCELED => [
            'color' => '#c12f30'
        ],
        self::STATE_HOLDED => [
            'color' => '#b9c1bd'
        ],
        self::STATE_PAYMENT_REVIEW => [
            'color' => '#ae61db'
        ]
    ];

    /**
     * @param Order $order
     * @param array $params
     *
     * @return bool
     * @throws \Exception
     */
    public static function changeOrderState(Order $order, $params = [])
    {
        $user = \Pimcore\Model\User::getById(0);
        $manager = Workflow\Manager\Factory::getManager($order, $user);

        if (!\Zend_Registry::isRegistered('pimcore_admin_user')) {
            \Zend_Registry::set('pimcore_admin_user', $user);
        } else if (is_null(\Zend_Registry::get('pimcore_admin_user'))) {
            \Zend_Registry::set('pimcore_admin_user', $user);
        }

        $params['action'] = 'change_order_state';

        if ($manager->validateAction($params['action'], $params['newState'], $params['newStatus'])) {
            try {
                $manager->performAction($params['action'], $params);
                \Pimcore\Logger::debug('CoreShop orderState update. OrderId: ' . $order->getId() . ', newState: "' . $params['newState'] . '", newStatus: "' . $params['newStatus'] .'"');
            } catch (\Exception $e) {
                throw new \Exception('changeOrderState Error: ' . $e->getMessage());
            }
        } else {
            throw new \Exception('changeOrderState Error: ' . $manager->getError());
        }

        \Zend_Registry::set('pimcore_admin_user', null);

        return true;
    }

    /**
     * @param Order $order
     *
     * @return array|string
     * @throws \Exception
     */
    public static function getOrderCurrentState(Order $order)
    {
        $user = \Pimcore\Model\User::getById(0);
        $manager = Workflow\Manager\Factory::getManager($order, $user);

        $state = $manager->getWorkflowStateForElement()->getState();
        $status = $manager->getWorkflowStateForElement()->getStatus();

        $info = [];

        if (!is_null($state)) {
            $info['state'] = $manager->getWorkflow()->getStateConfig($state);
            $info['state']['translatedLabel'] = self::_translateWorkflowLabel($info['state']['label']);
            $info['state']['color'] = self::$STATE_CONFIG[$state]['color'];
        }

        if (!is_null($status)) {
            $info['status'] = $manager->getWorkflow()->getStateConfig($status);
            $info['status']['translatedLabel'] = self::_translateWorkflowLabel($info['status']['label']);
        }

        return !empty($info) ? $info : false;
    }

    /**
     * @param Order $order
     *
     * @return array|string
     * @throws \Exception
     */
    public static function getOrderStateHistory(Order $order)
    {
        /* @var \Pimcore\Model\Element\Note\Listing $noteList */
        $noteList = new Note\Listing();
        $noteList->addConditionParam('type = ?', 'Order State Change');
        $noteList->addConditionParam('cid = ?', $order->getId());
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        return $noteList->load();
    }

    /**
     * get valid Order-States
     *
     * @return bool|array
     */
    public static function getValidOrderStates()
    {
        $config = Workflow\Config::getWorkflowManagementConfig(true);
        $orderClassId = Order::classId();

        foreach($config['workflows'] as $workflow) {
            if(array_key_exists('workflowSubject', $workflow)) {
                $subject = $workflow['workflowSubject'];

                if(array_key_exists('classes', $subject)) {
                    if(in_array($orderClassId, $subject['classes'])) {
                        return $workflow['states'];
                    }
                }
            }
        }

        return false;
    }

    /**
     * Because Pimcore Workflow does not allow to use the translateLabel() method, we need a custom one.
     *
     * @param $key
     * @return string
     */
    private static function _translateWorkflowLabel($key)
    {
        try {
            return \Pimcore\Model\Translation\Admin::getByKeyLocalized($key, false, true);
        } catch (\Exception $e) {
            return $key;
        }
    }
}

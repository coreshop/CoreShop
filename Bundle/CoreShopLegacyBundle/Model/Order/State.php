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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Order;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Order;
use Pimcore\Model\Element\Note;
use Pimcore\Version;

/**
 * Class State
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Order
 */
class State
{
    /**
     * State for Initialized Order
     */
    const STATE_INITIALIZED        = 'initialized';

    /**
     * State for New Order
     */
    const STATE_NEW                = 'new';

    /**
     * State for Pending Payment Order
     */
    const STATE_PENDING_PAYMENT    = 'pending_payment';

    /**
     * State for Processing Order
     */
    const STATE_PROCESSING         = 'processing';

    /**
     * State for Complete Order
     */
    const STATE_COMPLETE           = 'complete';

    /**
     * State for Closed Order
     */
    const STATE_CLOSED             = 'closed';

    /**
     * State for Canceled Order
     */
    const STATE_CANCELED           = 'canceled';

    /**
     * State for Order on Hold
     */
    const STATE_HOLDED             = 'holded';

    /**
     * Sate for Payment Review Order
     */
    const STATE_PAYMENT_REVIEW     = 'payment_review';

    /**
     * Status for Initialized Order
     */
    const STATUS_INITIALIZED        = 'initialized';

    /**
     * Status for Pending Order
     */
    const STATUS_PENDING            = 'pending';

    /**
     * Status for Pending Order Payment
     */
    const STATUS_PENDING_PAYMENT    = 'pending_payment';

    /**
     * Status for Processing Order
     */
    const STATUS_PROCESSING         = 'processing';

    /**
     * Status for Completed Order
     */
    const STATUS_COMPLETE           = 'complete';

    /**
     * Status for Closed Order
     */
    const STATUS_CLOSED             = 'closed';

    /**
     * Status for Canceled Order
     */
    const STATUS_CANCELED           = 'canceled';

    /**
     * Status for Order on Hold
     */
    const STATUS_HOLDED             = 'holded';

    /**
     * Status for Payment Review Order
     */
    const STATUS_PAYMENT_REVIEW     = 'payment_review';

    /**
     * @var array
     */
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
        $manager = \Pimcore\WorkflowManagement\Workflow\Manager\Factory::getManager($order, $user);

        if (!\Zend_Registry::isRegistered('pimcore_admin_user')) {
            \Zend_Registry::set('pimcore_admin_user', $user);
        } elseif (is_null(\Zend_Registry::get('pimcore_admin_user'))) {
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
        $manager = \Pimcore\WorkflowManagement\Workflow\Manager\Factory::getManager($order, $user);

        $state = $manager->getWorkflowStateForElement()->getState();
        $status = $manager->getWorkflowStateForElement()->getStatus();

        $info = [];

        if (!is_null($state)) {
            $info['state'] = $manager->getWorkflow()->getStateConfig($state);
            $info['state']['translatedLabel'] = self::_translateWorkflowLabel($info['state']['label']);
            $info['state']['color'] = self::$STATE_CONFIG[$state]['color'];
        }

        if (!is_null($status)) {
            $info['status'] = $manager->getWorkflow()->getStatusConfig($status);
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
        $orderClassId = Order::classId();

        $list = new \Pimcore\Model\Workflow\Listing();
        $list->load();

        foreach($list->getWorkflows() as $workflow) {
            if(is_array($workflow->getWorkflowSubject())) {
                $subject = $workflow->getWorkflowSubject();

                if (array_key_exists('classes', $subject)) {
                    if (in_array($orderClassId, $subject['classes'])) {
                        return $workflow->getStates();
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

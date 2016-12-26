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

use CoreShop\Model\Configuration;
use CoreShop\Model\Order;
use CoreShop\Mail;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Note;
use Pimcore\WorkflowManagement\Workflow;

/**
 * Class State
 * @package CoreShop\Model\Order
 */
class State
{
    const STATE_NEW                = 'new';
    const STATE_PENDING_PAYMENT    = 'pending_payment';
    const STATE_PROCESSING         = 'processing';
    const STATE_COMPLETE           = 'complete';
    const STATE_CLOSED             = 'closed';
    const STATE_CANCELED           = 'canceled';
    const STATE_HOLDED             = 'holded';
    const STATE_PAYMENT_REVIEW     = 'payment_review';

    const STATUS_PENDING            = 'pending';
    const STATUS_PENDING_PAYMENT    = 'pending_payment';
    const STATUS_PROCESSING         = 'processing';
    const STATUS_COMPLETE           = 'complete';
    const STATUS_CLOSED             = 'closed';
    const STATUS_CANCELED           = 'canceled';
    const STATUS_HOLDED             = 'holded';
    const STATUS_PAYMENT_REVIEW     = 'payment_review';

    const ORDER_STATE_CONFIRMATION_MAIL     = 'sendOrderConfirmationMail';
    const ORDER_STATE_STATUS_MAIL           = 'sendOrderStatusMail';

    protected static $STATUS_CONFIG = [

        self::STATUS_PENDING => [
            'color' => '#9bc4c4'
        ],
        self::STATUS_PENDING_PAYMENT => [
            'color' => '#d0c31f'
        ],
        self::STATUS_PROCESSING => [
            'color' => '#3081ba'
        ],
        self::STATUS_COMPLETE => [
            'color' => '#73a623'
        ],
        self::STATUS_CLOSED => [
            'color' => '#ffc301'
        ],
        self::STATUS_CANCELED => [
            'color' => '#c12f30'
        ],
        self::STATUS_HOLDED => [
            'color' => '#b9c1bd'
        ],
        self::STATUS_PAYMENT_REVIEW => [
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

        if (!is_null($state)) {
            $decorator = new Workflow\Decorator($manager->getWorkflow());
            $title = $decorator->getStatusLabel($status);

            return [
                'state' => $state,
                'status' => $status,
                'name'  => $title,
                'color' => self::$STATUS_CONFIG[$status]['color']
            ];
        }

        return $state;
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
     *
     * @deprecated
     *
     * Process OrderState for Order.
     *
     * @param Order $order
     * @return bool
     * @throws \Exception
     */
    public function processStep(Order $order)
    {
        //implement into workflows!
        if ($this->getShipped()) {
            if ((bool) Configuration::get('SYSTEM.SHIPMENT.CREATE')) {
                $shipments = $order->getShipments();

                if (count($shipments) === 0) {
                    $order->createShipmentForAllItems();
                }
            }
        }

        return true;
    }
}

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
            'color' => '#c4d5a9'
        ],
        self::STATUS_PENDING_PAYMENT => [
            'color' => '#eccd1d'
        ],
        self::STATUS_PROCESSING => [
            'color' => '#69acbf'
        ],
        self::STATUS_COMPLETE => [
            'color' => '#89a550'
        ],
        self::STATUS_CLOSED => [
            'color' => '#aabbd7'
        ],
        self::STATUS_CANCELED => [
            'color' => '#a44948'
        ],
        self::STATUS_HOLDED => [
            'color' => '#e49f68'
        ],
        self::STATUS_PAYMENT_REVIEW => [
            'color' => '#dc833f'
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
            } catch (\Exception $e) {
                throw new \Exception('changeOrderState Error: ' . $e->getMessage());
            }

        } else {
            throw new \Exception('changeOrderState Error: ' . $manager->getError());
        }

        \Zend_Registry::set('pimcore_admin_user', NULL);

        return TRUE;
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

        $state = $manager->getWorkflowStateForElement()->getStatus();

        if(!is_null($state)) {

            $decorator = new Workflow\Decorator($manager->getWorkflow());
            $title = $decorator->getStatusLabel($state);

            return [
                'name'  => $title,
                'color' => self::$STATUS_CONFIG[ $state ]['color']
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
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function processStep(Order $order)
    {
        $previousState = $order->getOrderState();
        //Check if new OrderState is the same as the current one
        if ($order->getOrderState() instanceof self) {
            if ($order->getOrderState()->getId() === $this->getId()) {
                return false;
            }
        }

        if ($this->getShipped()) {
            if ((bool) Configuration::get('SYSTEM.SHIPMENT.CREATE')) {
                $shipments = $order->getShipments();

                if (count($shipments) === 0) {
                    $order->createShipmentForAllItems();
                }
            }
        }

        if ($this->getInvoice()) {
            if ((bool) Configuration::get('SYSTEM.INVOICE.CREATE')) {
                $invoices = $order->getInvoices();

                if (count($invoices) === 0) {
                    $order->createInvoiceForAllItems();
                }
            }
        }

        if ($this->getEmail()) {
            $emailDocument = $this->getEmailDocument($order->getLang());
            $emailDocument = Document::getByPath($emailDocument);

            Mail::sendOrderMail($emailDocument, $order, $this);
        }

        $order->setOrderState($this);
        $order->save();


        return true;
    }

}

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

        if($orderObject instanceof Order) {
            if( $currentStatus === $newStatus) {
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

        if($orderObject instanceof Order) {

            //create invoice, if allowed.
            if(self::checkAutomatedInvoicePossibility($orderObject, $oldStatus, $newStatus)) {
                $orderObject->createInvoiceForAllItems();
            }

            //send confirmation order mail.
            if(isset($additional[Order\State::ORDER_STATE_CONFIRMATION_MAIL]) && $additional[Order\State::ORDER_STATE_CONFIRMATION_MAIL] === 'yes') {
                $confirmationMailPath = Configuration::get('SYSTEM.MAIL.ORDER.STATES.CONFIRMATION.' . strtoupper($orderObject->getLang()));
                $emailDocument = Document::getByPath($confirmationMailPath);

                if($emailDocument instanceof Document\Email) {
                    Mail::sendOrderMail($emailDocument, $orderObject);
                }
            }

            //send order update status mail.
            if(isset($additional[Order\State::ORDER_STATE_STATUS_MAIL]) && $additional[Order\State::ORDER_STATE_STATUS_MAIL] === 'yes') {
                $updateMailPath = Configuration::get('SYSTEM.MAIL.ORDER.STATES.UPDATE.' . strtoupper($orderObject->getLang()));
                $emailDocument = Document::getByPath($updateMailPath);

                if($emailDocument instanceof Document\Email) {
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
        if ((bool) Configuration::get('SYSTEM.INVOICE.CREATE') === FALSE) {
            return FALSE;
        }

        $allowedStatuses = [Order\State::STATUS_PENDING_PAYMENT, Order\State::STATUS_PAYMENT_REVIEW];

        if(!in_array($oldStatus, $allowedStatuses)) {
            return FALSE;
        }

        $invoices = $order->getInvoices();

        if (count($invoices) !== 0) {
           return FALSE;
        }

        return TRUE;
    }
}

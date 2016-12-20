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

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Configuration;
use CoreShop\Model\Order;
use CoreShop\Mail;
use Pimcore\Model\Document;
use Pimcore\WorkflowManagement\Workflow;

/**
 * Class State
 * @package CoreShop\Model\Order
 */
class State extends AbstractModel
{
    const STATUS_NEW                = 'new';
    const STATUS_PENDING_PAYMENT    = 'pending_payment';
    const STATUS_PROCESSING         = 'processing';
    const STATUS_COMPLETE           = 'complete';
    const STATUS_CLOSED             = 'closed';
    const STATUS_CANCELED           = 'canceled';
    const STATUS_HOLDED             = 'holded';
    const STATUS_PAYMENT_REVIEW     = 'payment_review';

    const ORDER_STATE_CONFIRMATION_MAIL     = 'sendOrderConfirmationMail';
    const ORDER_STATE_STATUS_MAIL           = 'sendOrderStatusMail';

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
                throw new \Exception('changeOrderState Error: ' .$e->getMessage());
            }

        } else {
            throw new \Exception('changeOrderState Error: ' . $manager->getError());
        }

        \Zend_Registry::set('pimcore_admin_user', NULL);

        return TRUE;
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

        if ($this->getAccepted()) {
        }

        if ($this->getShipped()) {
            if ((bool) Configuration::get('SYSTEM.SHIPMENT.CREATE')) {
                $shipments = $order->getShipments();

                if (count($shipments) === 0) {
                    $order->createShipmentForAllItems();
                }
            }
        }

        if ($this->getPaid()) {
            //\CoreShop::actionHook("paymentConfirmation", array("order" => $order));
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

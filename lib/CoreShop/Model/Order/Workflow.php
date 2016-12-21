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
use CoreShop\Exception;

/**
 * Class Workflow
 * @package CoreShop\Model\Order
 */
class Workflow
{
    /**
     * @fixme: not supported right now.
     * @param \Zend_EventManager_Event $event
     *
     * @throws Exception\UnsupportedException
     */
    public static function beforeDispatchOrderChange($event)
    {
        throw new Exception\UnsupportedException('before event is not implemented right now.');
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

        if( $orderObject instanceof Object\CoreShopOrder ) {

            if( isset($additional[Order\State::ORDER_STATE_CONFIRMATION_MAIL]) && $additional[Order\State::ORDER_STATE_CONFIRMATION_MAIL] === 'yes') {
                $confirmationMailPath = Configuration::get('SYSTEM.MAIL.ORDER.STATES.CONFIRMATION.' . strtoupper($orderObject->getLang()));
                $emailDocument = Document::getByPath($confirmationMailPath);

                if( $emailDocument instanceof Document\Email) {
                    Mail::sendOrderMail($emailDocument, $orderObject);
                }
            }

            if( isset($additional[Order\State::ORDER_STATE_CONFIRMATION_MAIL]) && $additional[Order\State::ORDER_STATE_CONFIRMATION_MAIL] === 'yes') {
                $updateMailPath = Configuration::get('SYSTEM.MAIL.ORDER.STATES.UPDATE.' . strtoupper($orderObject->getLang()));
                $emailDocument = Document::getByPath($updateMailPath);

                if( $emailDocument instanceof Document\Email) {
                    Mail::sendOrderMail($emailDocument, $orderObject);
                }
            }
        }
    }

    /**
     * @param \Zend_EventManager_Event $event
     */
    public static function dispatchOrderChangeFailed($event)
    {
        $exception = $event->getParam('exception');
        \Pimcore\Logger::err('CoreShop Workflow OrderChange failed. Reason: ' . $exception->getMessage() );
    }
}

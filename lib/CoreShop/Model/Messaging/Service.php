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

namespace CoreShop\Model\Messaging;

use CoreShop\Model\Configuration;
use CoreShop\Model\Mail\Rule;
use CoreShop\Model\Order;
use CoreShop\Model\Product;
use CoreShop\Model\Shop;
use CoreShop\Model\User;
use Pimcore\Model\Document\Email;

/**
 * Class Service
 * @package CoreShop\Model\Messaging
 */
class Service
{
    /**
     * Handles the Request and creates Thread/Message.
     *
     * @param $params
     * @param $language
     * @param $sendContact
     * @param $sendCustomer
     *
     * @return array
     */
    public static function handleRequestAndCreateThread($params, $language, $sendContact = true, $sendCustomer = true)
    {
        if (!$params['contact']) {
            return [
                'success' => false,
                'message' => 'Subject is not set',
            ];
        }

        if (!$params['message']) {
            return [
                'success' => false,
                'message' => 'Message is not set',
            ];
        }

        if (!$params['email']) {
            return [
                'success' => false,
                'message' => 'E-Mail is not set',
            ];
        }

        $thread = Thread::getByField('token', $params['token']);

        //Check if there is already an open thread for the email address
        if (!$thread instanceof Thread) {
            $thread = Thread::searchThread($params['email'], $params['contact'], Shop::getShop()->getId(), $params['order'], $params['product']);
        }

        if (!$thread instanceof Thread) {
            $thread = Thread::create();
            $thread->setEmail($params['email']);
            $thread->setShopId(Shop::getShop()->getId());
            $thread->setStatusId(Configuration::get('SYSTEM.MESSAGING.THREAD.STATE.NEW'));

            if (\CoreShop::getTools()->getUser() instanceof User) {
                $thread->setUser(\CoreShop::getTools()->getUser());
            }

            if ($params['orderNumber']) {
                //Check Order Reference
                $order = Order::getByOrderNumber($params['orderNumber'], 1);

                if ($order instanceof Order) {
                    if ($order->getCustomer() instanceof User) {
                        if ($order->getCustomer()->getEmail() === $params['email']) {
                            $thread->setOrder($order);
                        }
                    }
                }
            }

            if ($params['product']) {
                $product = Product::getById($params['product']);

                if ($product instanceof Product) {
                    $thread->setProduct($product);
                }
            }

            $customer = User::getUserByEmail($params['email']);

            if ($customer instanceof User) {
                $thread->setUser($customer);
            }

            $thread->setContact(Contact::getById($params['contact']));
            $thread->setToken(uniqid());
            $thread->setLanguage($language);
            $thread->save();
        }

        $message = $thread->createMessage($params['message']);

        if ($sendContact) {
            //Send Contact
            $message->sendNotification('contact', $thread->getContact()->getEmail());
        }

        if ($sendCustomer) {
            //Send Customer Info Mail
            $message->sendNotification('customer', $thread->getEmail());
        }

        return [
            'success' => true,
            'message' => $message,
            'thread' => $thread,
        ];
    }
}

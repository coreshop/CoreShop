<?php

namespace CoreShop\Model\Messaging;

use CoreShop\Model\Configuration;
use CoreShop\Model\Order;
use CoreShop\Model\Product;
use CoreShop\Model\User;
use CoreShop\Tool;
use Pimcore\Model\Document\Email;

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
            return array(
                'success' => false,
                'message' => 'Subject is not set',
            );
        }

        if (!$params['message']) {
            return array(
                'success' => false,
                'message' => 'Message is not set',
            );
        }

        if (!$params['email']) {
            return array(
                'success' => false,
                'message' => 'E-Mail is not set',
            );
        }

        $thread = Thread::getByField('token', $params['token']);

        //Check if there is already an open thread for the email address
        if (!$thread instanceof Thread) {
            $thread = Thread::searchThread($params['email'], $params['contact'], $params['order'], $params['product']);
        }

        if (!$thread instanceof Thread) {
            $thread = new Thread();
            $thread->setEmail($params['email']);
            $thread->setStatusId(Configuration::get('SYSTEM.MESSAGING.THREAD.STATE.NEW'));

            if (Tool::getUser() instanceof User) {
                $thread->setUser(Tool::getUser());
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
            $contactEmailDocument = Email::getById(Configuration::get('SYSTEM.MESSAGING.MAIL.CONTACT.'.strtoupper($thread->getLanguage())));
            $message->sendNotification($contactEmailDocument, $thread->getContact()->getEmail());
        }

        if ($sendCustomer) {
            //Send Customer Info Mail
            $customerInfoMail = Email::getById(Configuration::get('SYSTEM.MESSAGING.MAIL.CUSTOMER.'.strtoupper($thread->getLanguage())));
            $message->sendNotification($customerInfoMail, $thread->getEmail());
        }

        return array(
            'success' => true,
            'message' => $message,
            'thread' => $thread,
        );
    }
}

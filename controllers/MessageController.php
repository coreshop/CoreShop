<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

use CoreShop\Controller\Action;

class CoreShop_MessageController extends Action
{
    public function contactAction()
    {
        $this->view->contacts = \CoreShop\Model\Messaging\Contact::getList()->load();
        $this->view->params = $this->getAllParams();
        $thread = null;

        if($this->view->params['token']) {
            $thread = \CoreShop\Model\Messaging\Thread::getByField("token", $this->view->params['token']);

            if($thread instanceof \CoreShop\Model\Messaging\Thread) {
                $this->view->params['contactId'] = $thread->getContactId();
                $this->view->params['email'] = $thread->getEmail();

                if($thread->getOrder() instanceof \CoreShop\Model\Order)
                    $this->view->params['orderNumber'] = $thread->getOrder()->getOrderNumber();
            }
        }

        if($this->getRequest()->isPost()) {
            $params = $this->getAllParams();
            $success = true;

            if(!$params['contact']) {
                $this->view->error = $this->view->translate("Subject is not set");

                $success = false;
            }

            if(!$params['message']) {
                $this->view->error = $this->view->translate("Message is not set");

                $success = false;
            }

            if(!$params['email']) {
                $this->view->error = $this->view->translate("E-Mail is not set");

                $success = false;
            }

            if($success) {
                //Check if there is already an open thread for the email address
                if(!$thread instanceof CoreShop\Model\Messaging\Thread)
                    $thread = \CoreShop\Model\Messaging\Thread::getOpenByEmailAndContact($params['email'], $params['contact']);

                if(!$thread instanceof \CoreShop\Model\Messaging\Thread) {
                    $thread = new \CoreShop\Model\Messaging\Thread();
                    $thread->setEmail($params['email']);
                    $thread->setStatusId(\CoreShop\Model\Configuration::get("SYSTEM.MESSAGING.THREAD.STATE.NEW"));

                    if(\CoreShop\Tool::getUser() instanceof CoreShop\Model\User) {
                        $thread->setUser(\CoreShop\Tool::getUser());
                    }

                    if($params['orderNumber'])
                    //Check Order Reference
                    $order = \CoreShop\Model\Order::create()->getByOrderNumber($params['orderNumber']);

                    if($order instanceof \CoreShop\Model\Order) {
                        if($order->getCustomer() instanceof \CoreShop\Model\User) {
                            if($order->getCustomer()->getEmail() === $params['email']) {
                                $thread->setOrder($order);
                            }
                        }
                    }

                    $customer = \CoreShop\Model\User::getUserByEmail($params['email']);

                    if($customer instanceof \CoreShop\Model\User) {
                        $thread->setUser($customer);
                    }

                    $thread->setContact(\CoreShop\Model\Messaging\Contact::getById($params['contact']));
                    $thread->setToken(uniqid());
                    $thread->setLanguage($this->language);
                    $thread->save();
                }

                $message = $thread->createMessage($params['message']);

                
                //Send Contact
                $contactEmailDocument = \Pimcore\Model\Document\Email::getById(\CoreShop\Model\Configuration::get("SYSTEM.MESSAGING.MAIL.CONTACT." . strtoupper($thread->getLanguage())));
                $message->sendNotification($contactEmailDocument, $thread->getContact()->getEmail());

                //Send Customer Info Mail
                $customerInfoMail = \Pimcore\Model\Document\Email::getById(\CoreShop\Model\Configuration::get("SYSTEM.MESSAGING.MAIL.CUSTOMER." . strtoupper($thread->getLanguage())));
                $message->sendNotification($customerInfoMail, $thread->getEmail());
            }

            $this->view->success = $success;
        }
    }
}

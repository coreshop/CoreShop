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

namespace CoreShop\Model\Messaging;

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Messaging\Thread\State;
use CoreShop\Model\Order;
use CoreShop\Model\User;

class Thread extends AbstractModel {

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var User
     */
    public $user;

    /**
     * @var int
     */
    public $orderId;

    /**
     * @var Order
     */
    public $order;

    /**
     * @var int
     */
    public $statusId;

    /**
     * @var State
     */
    public $status;

    /**
     * @var string
     */
    public $token;

    /**
     * @var int
     */
    public $contactId;

    /**
     * @var Contact
     */
    public $contact;

    /**
     * @var string
     */
    public $language;

    /**
     * @var string
     */
    public $email;

    /**
     * Get Open threads by email
     *
     * @param $email string
     * @param $contactId int
     *
     * @return Thread|null
     */
    public static function getOpenByEmailAndContact($email, $contactId) {
        $list = self::getList();
        $list->setCondition("email = ? AND contactId = ?", array($email, $contactId));
        $list = $list->load();

        foreach($list as $thread) {
            if(!$thread->getStatus()->getFinished()) {
                return $thread;
            }
        }

        return null;
    }

    /**
     * Create a new message for thread
     *
     * @param $messageText string
     * @return Message
     * @throws \Exception
     */
    public function createMessage($messageText) {
        $message = new Message();
        $message->setThread($this);
        $message->setMessage($messageText);
        $message->setRead(false);
        $message->save();

        return $message;
    }

    /**
     * Get all messages
     *
     * @return Message[]
     */
    public function getMessages() {
        $list = new Message\Listing();
        $list->setCondition("threadId = ?", array($this->getId()));

        return $list->load();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if(!$this->user instanceof User) {
            $this->user = User::getById($this->userId);
        }

        return $this->user;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function setUser($user)
    {
        if(!$user instanceof User) {
            throw new \Exception("\$user must be instance of User");
        }

        $this->user = $user;
        $this->userId = $user->getId();
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        if(!$this->order instanceof Order) {
            $this->order = Order::getById($this->orderId);
        }

        return $this->order;
    }

    /**
     * @param Order $order
     * @throws \Exception
     */
    public function setOrder($order)
    {
        if(!$order instanceof Order) {
            throw new \Exception("\$order must be instance of Order");
        }

        $this->order = $order;
        $this->orderId = $order->getId();
    }

    /**
     * @return int
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * @param int $statusId
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
    }

    /**
     * @return State
     */
    public function getStatus()
    {
        if(!$this->status instanceof State) {
            $this->status = State::getById($this->statusId);
        }

        return $this->status;
    }

    /**
     * @param State $status
     * @throws \Exception
     */
    public function setStatus($status)
    {
        if (!$status instanceof State) {
            throw new \Exception("\$status must be instance of State");
        }

        $this->status = $status;
        $this->statusId = $status->getId();
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getContactId()
    {
        return $this->contactId;
    }

    /**
     * @param int $contactId
     */
    public function setContactId($contactId)
    {
        $this->contactId = $contactId;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        if(!$this->contact instanceof Contact) {
            $this->contact = Contact::getById($this->contactId);
        }

        return $this->contact;
    }

    /**
     * @param Contact $contact
     * @throws \Exception
     */
    public function setContact($contact)
    {
        if (!$contact instanceof Contact) {
            throw new \Exception("\$contact must be instance of Contact");
        }

        $this->contact = $contact;
        $this->contactId = $contact->getId();
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
}
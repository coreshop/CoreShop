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
use CoreShop\Model\Configuration;
use Pimcore\Mail;
use Pimcore\Model\Document\Email;
use Pimcore\Model\User;

class Message extends AbstractModel {

    /**
     * @var int
     */
    public $id;

    /**
     * @var $id
     */
    public $threadId;

    /**
     * @var Thread
     */
    public $thread;

    /**
     * @var User
     */
    public $adminUserId;

    /**
     * @var string
     */
    public $message;
    
    /**
     * @var boolean
     */
    public $read;

    /**
     * Sends the User the message as email
     */
    public function sendCustomerEmail() {
        $emailDocument = Email::getById(Configuration::get("SYSTEM.MESSAGING.MAIL.CUSTOMER." . $this->getThread()->getLanguage()));
        
        if($emailDocument instanceof Email) {
            $mail = new Mail();
            $mail->setDocument($emailDocument);
            $mail->setParams(array("message" => $this->getMessage(), "messageObject" => $this));
            $mail->setEnableLayoutOnPlaceholderRendering(false);
            $mail->addTo($this->getThread()->getEmail());
            $mail->send();
        }
        else {
            \Logger::warn("Email Document for Messages not found!");
        }
    }

    /**
     * Sends the User the message as email
     */
    public function sendContactEmail() {
        $emailDocument = Email::getById(Configuration::get("SYSTEM.MESSAGING.MAIL.CONTACT." . $this->getThread()->getLanguage()));

        if($emailDocument instanceof Email) {
            $mail = new Mail();
            $mail->setDocument($emailDocument);
            $mail->setParams(array("message" => $this->getMessage(), "messageObject" => $this));
            $mail->setEnableLayoutOnPlaceholderRendering(false);
            $mail->addTo($this->getThread()->getContact()->getEmail());
            $mail->send();
        }
        else {
            \Logger::warn("Email Document for Messages not found!");
        }
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
     * @return mixed
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * @param mixed $threadId
     */
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;
    }

    /**
     * @return Thread
     */
    public function getThread()
    {
        if(!$this->thread instanceof Thread) {
            $this->thread = Thread::getById($this->threadId);
        }

        return $this->thread;
    }

    /**
     * @param Thread $thread
     * @throws \Exception
     */
    public function setThread($thread)
    {
        if(!$thread instanceof Thread) {
            throw new \Exception("$thread must be instance of Thread");
        }

        $this->thread = $thread;
        $this->threadId = $thread->getId();
    }

    /**
     * @return User
     */
    public function getAdminUserId()
    {
        return $this->adminUserId;
    }

    /**
     * @param User $adminUserId
     */
    public function setAdminUserId($adminUserId)
    {
        $this->adminUserId = $adminUserId;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return boolean
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @param boolean $read
     */
    public function setRead($read)
    {
        $this->read = $read;
    }
}
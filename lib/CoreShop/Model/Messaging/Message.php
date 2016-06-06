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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\Model\Messaging;

use CoreShop\Model\AbstractModel;
use CoreShop\Mail;
use Pimcore\Model\Document;
use Pimcore\Model\Document\Email;

class Message extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var
     */
    public $threadId;

    /**
     * @var Thread
     */
    public $thread;

    /**
     * @var int
     */
    public $adminUserId;

    /**
     * @var string
     */
    public $message;

    /**
     * @var bool
     */
    public $read;

    /**
     * @var \DateTime
     */
    public $creationDate;

    /**
     * Send email to recipient with Message.
     *
     * @param Document $mailDocument
     * @param string   $recipient
     *
     * @throws \Exception
     */
    public function sendNotification(Document $mailDocument, $recipient)
    {
        if ($mailDocument instanceof Email) {
            $mail = new Mail();
            $mail->setDocument($mailDocument);
            $mail->setParams(array('message' => $this->getMessage(), 'messageObject' => $this));
            $mail->setEnableLayoutOnPlaceholderRendering(false);
            $mail->addTo($recipient);
            $mail->send();
        } else {
            \Logger::warn('Email Document for Messages not found!');
        }
    }

    /**
     * Save Message.
     */
    public function save()
    {
        if (!$this->getId()) {
            if (!$this->getCreationDate()) {
                $this->setCreationDate(time());
            }
        }

        parent::save();
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
        if (!$this->thread instanceof Thread) {
            $this->thread = Thread::getById($this->threadId);
        }

        return $this->thread;
    }

    /**
     * @param Thread $thread
     *
     * @throws \Exception
     */
    public function setThread($thread)
    {
        if (!$thread instanceof Thread) {
            throw new \Exception("$thread must be instance of Thread");
        }

        $this->thread = $thread;
        $this->threadId = $thread->getId();
    }

    /**
     * @return int
     */
    public function getAdminUserId()
    {
        return $this->adminUserId;
    }

    /**
     * @param int $adminUserId
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
     * @return bool
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @param bool $read
     */
    public function setRead($read)
    {
        $this->read = $read;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }
}

<?php

namespace CoreShop\Bundle\PimcoreBundle\Event;

use CoreShop\Component\Pimcore\Mail;
use Pimcore\Model\Document\Email;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MailEvent extends GenericEvent
{
    /**
     * @var Email
     */
    protected $emailDocument;

    /**
     * @var Mail
     */
    protected $mail;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var bool
     */
    protected $shouldSendMail = true;

    /**
     * @param $subject
     * @param Email $emailDocument
     * @param Mail $mail
     * @param array $params
     */
    public function __construct($subject, Email $emailDocument, Mail $mail, array $params = [])
    {
        parent::__construct($subject);

        $this->emailDocument = $emailDocument;
        $this->mail = $mail;
        $this->params = $params;
    }

    /**
     * @return Email
     */
    public function getEmailDocument()
    {
        return $this->emailDocument;
    }

    /**
     * @return Mail
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return bool
     */
    public function getShouldSendMail()
    {
        return $this->shouldSendMail;
    }

    /**
     * @param bool $shouldSendMail
     */
    public function setShouldSendMail($shouldSendMail)
    {
        $this->shouldSendMail = $shouldSendMail;
    }
}
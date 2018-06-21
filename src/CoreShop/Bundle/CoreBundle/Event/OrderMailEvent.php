<?php

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Order\Model\OrderInterface;
use Pimcore\Mail;
use Pimcore\Model\Document\Email;
use Symfony\Component\EventDispatcher\Event;

final class OrderMailEvent extends Event
{
    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var Email
     */
    protected $orderEmailDocument;

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
     * @param OrderInterface $order
     * @param Email $orderEmailDocument
     * @param Mail $mail
     * @param array $params
     */
    public function __construct(OrderInterface $order, Email $orderEmailDocument, Mail $mail, array $params = [])
    {
        $this->order = $order;
        $this->orderEmailDocument = $orderEmailDocument;
        $this->mail = $mail;
        $this->params = $params;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return Email
     */
    public function getOrderEmailDocument()
    {
        return $this->orderEmailDocument;
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
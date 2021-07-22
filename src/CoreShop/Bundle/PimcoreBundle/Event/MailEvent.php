<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

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
     * @param mixed $subject
     * @param Email $emailDocument
     * @param Mail  $mail
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

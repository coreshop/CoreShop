<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\PimcoreBundle\Event;

use CoreShop\Component\Pimcore\Mail;
use Pimcore\Model\Document\Email;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MailEvent extends GenericEvent
{
    protected array $params;

    protected bool $shouldSendMail = true;

    public function __construct(
        $subject,
        protected Email $emailDocument,
        protected Mail $mail,
        array $params = [],
    ) {
        parent::__construct($subject);
        $this->params = $params;
    }

    public function getEmailDocument(): Email
    {
        return $this->emailDocument;
    }

    public function getMail(): Mail
    {
        return $this->mail;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getShouldSendMail(): bool
    {
        return $this->shouldSendMail;
    }

    public function setShouldSendMail(bool $shouldSendMail): void
    {
        $this->shouldSendMail = $shouldSendMail;
    }
}

<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\PimcoreBundle\Event;

use CoreShop\Component\Pimcore\Mail;
use Pimcore\Model\Document\Email;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MailEvent extends GenericEvent
{
    protected array $params;

    protected bool $shouldSendMail = true;

    public function __construct($subject, protected Email $emailDocument, protected Mail $mail, array $params = [])
    {
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

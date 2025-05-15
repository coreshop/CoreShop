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

namespace CoreShop\Bundle\PimcoreBundle\Mail;

use CoreShop\Bundle\PimcoreBundle\Event\MailEvent;
use CoreShop\Bundle\PimcoreBundle\Events;
use CoreShop\Component\Pimcore\Mail;
use Pimcore\Model\Document\Email;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class MailProcessor implements Mail\MailProcessorInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function sendMail(Email $emailDocument, $subject = null, $recipients = null, array $attachments = [], array $params = []): bool
    {
        $mailHasBeenSent = false;

        $mail = new Mail();

        foreach ($attachments as $attachment) {
            if (is_array($attachment)) {
                $mail->attach($attachment['body'], $attachment['name'], $attachment['content-type']);
            }
        }

        $mail->setDocument($emailDocument);
        $mail->setParams($params);
        $mail->addRecipients($recipients);

        //BC Remove with 3.1
        if (method_exists($mail, 'setEnableLayoutOnPlaceholderRendering')) {
            $mail->setEnableLayoutOnPlaceholderRendering(false);
        } elseif (method_exists($mail, 'setEnableLayoutOnRendering')) {
            $mail->setEnableLayoutOnRendering(false);
        }

        $mailEvent = new MailEvent(
            $subject,
            $emailDocument,
            $mail,
            $params,
        );

        $this->eventDispatcher->dispatch($mailEvent, Events::PRE_MAIL_SEND);

        if ($mailEvent->getShouldSendMail()) {
            $mail->send();
            $mailHasBeenSent = true;
        }

        $this->eventDispatcher->dispatch($mailEvent, Events::POST_MAIL_SEND);

        return $mailHasBeenSent;
    }
}

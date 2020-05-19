<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\PimcoreBundle\Mail;

use CoreShop\Bundle\PimcoreBundle\Event\MailEvent;
use CoreShop\Bundle\PimcoreBundle\Events;
use CoreShop\Component\Pimcore\Mail;
use Pimcore\Model\Document\Email;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class MailProcessor implements MailProcessorInterface
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function sendMail(Email $emailDocument, $subject = null, $recipients = null, array $attachments = [], array $params = []): bool
    {
        $mailHasBeenSent = false;

        $mail = new Mail();

        foreach ($attachments as $attachment) {
            if ($attachment instanceof \Swift_Mime_SimpleMimeEntity) {
                $mail->attach($attachment);
            }
        }

        $mail->setDocument($emailDocument);
        $mail->setParams($params);
        $mail->addRecipients($recipients);
        $mail->setEnableLayoutOnPlaceholderRendering(false);

        $mailEvent = new MailEvent(
            $subject,
            $emailDocument,
            $mail,
            $params
        );

        $this->eventDispatcher->dispatch(Events::PRE_MAIL_SEND, $mailEvent);

        if ($mailEvent->getShouldSendMail()) {
            $mail->send();
            $mailHasBeenSent = true;
        }

        $this->eventDispatcher->dispatch(Events::POST_MAIL_SEND, $mailEvent);

        return $mailHasBeenSent;
    }
}

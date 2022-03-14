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

namespace CoreShop\Bundle\CoreBundle\EventListener\Order;

use CoreShop\Bundle\PimcoreBundle\Event\MailEvent;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Notes;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use CoreShop\Component\Pimcore\Mail;
use Pimcore\Model\Document\Email;

final class OrderMailNoteEventListener
{
    public function __construct(private NoteServiceInterface $noteService)
    {
    }

    public function onOrderMailSent(MailEvent $mailEvent): void
    {
        $subject = $mailEvent->getSubject();
        $params = $mailEvent->getParams();

        if ($subject instanceof OrderInterface) {
            $this->addOrderNote($subject, $mailEvent->getEmailDocument(), $mailEvent->getMail(), $params);
        }
    }

    private function addOrderNote(OrderInterface $order, Email $emailDocument, Mail $mail, array $params = []): void
    {
        /** @psalm-suppress InvalidArgument */
        $noteInstance = $this->noteService->createPimcoreNoteInstance($order, Notes::NOTE_EMAIL);

        $noteInstance->setTitle('Order Mail');

        $noteInstance->addData('document', 'text', $emailDocument->getId());
        /** @psalm-suppress InternalMethod */
        $noteInstance->addData('subject', 'text', $mail->getSubjectRendered());

        $mailTos = [];
        if (isset($params['recipient']) && !empty($params['recipient'])) {
            $recipients = $params['recipient'];

            if (is_array($recipients)) {
                foreach ($recipients as $mail => $name) {
                    if ($name) {
                        $mailTos[] = sprintf('%s <%s>', $name, $mail);
                    } else {
                        $mailTos[] = $mail;
                    }
                }
            } elseif (is_string($recipients)) {
                $mailTos[] = $recipients;
            }
        } else {
            $recipients = $mail->getTo();

            foreach ($recipients as $recipient) {
                $mailTos[] = sprintf('%s <%s>', $recipient->getName(), $recipient->getAddress());
            }
        }
        
        $noteInstance->addData('recipient', 'text', (empty($mailTos) ? '--' : implode(', ', $mailTos)));

        unset($params['recipient']);

        foreach ($params as $key => $value) {
            if (is_string($value)) {
                $noteInstance->addData($key, 'text', $value);
            }
        }

        $this->noteService->storeNoteForEmail($noteInstance, $emailDocument);
    }
}

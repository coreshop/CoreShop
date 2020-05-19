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

namespace CoreShop\Bundle\CoreBundle\EventListener\Order;

use CoreShop\Bundle\PimcoreBundle\Event\MailEvent;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Notes;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use CoreShop\Component\Pimcore\Mail;
use Pimcore\Model\Document\Email;

final class OrderMailNoteEventListener
{
    private $noteService;

    public function __construct(NoteServiceInterface $noteService)
    {
        $this->noteService = $noteService;
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
        $noteInstance = $this->noteService->createPimcoreNoteInstance($order, Notes::NOTE_EMAIL);

        $noteInstance->setTitle('Order Mail');

        $noteInstance->addData('document', 'text', $emailDocument->getId());
        $noteInstance->addData('subject', 'text', $mail->getSubjectRendered());

        $mailTos = [];
        $recipients = isset($params['recipient']) && !empty($params['recipient']) ? $params['recipient'] : $mail->getTo();

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

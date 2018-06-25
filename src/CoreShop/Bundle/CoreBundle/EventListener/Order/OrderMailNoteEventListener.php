<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\EventListener\Order;

use CoreShop\Bundle\PimcoreBundle\Event\MailEvent;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Notes;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use CoreShop\Component\Pimcore\Mail;
use Pimcore\Model\Document\Email;

final class OrderMailNoteEventListener
{
    /**
     * @var NoteServiceInterface
     */
    private $noteService;

    /**
     * @param NoteServiceInterface $noteService
     */
    public function __construct(NoteServiceInterface $noteService)
    {
        $this->noteService = $noteService;
    }

    /**
     * @param MailEvent $mailEvent
     */
    public function onOrderMailSent(MailEvent $mailEvent)
    {
        $subject = $mailEvent->getSubject();
        $params = $mailEvent->getParams();

        if ($subject instanceof OrderInterface) {
            $this->addOrderNote($subject, $mailEvent->getEmailDocument(), $mailEvent->getMail(), $params);
        }
    }

    /**
     * @param OrderInterface $order
     * @param Email $emailDocument
     * @param Mail $mail
     * @param array $params
     * @return bool
     */
    private function addOrderNote(OrderInterface $order, Email $emailDocument, Mail $mail, $params = [])
    {
        $noteInstance = $this->noteService->createPimcoreNoteInstance($order, Notes::NOTE_EMAIL);

        $noteInstance->setTitle('Order Mail');

        $noteInstance->addData('document', 'text', $emailDocument->getId());
        $noteInstance->addData('subject', 'text', $mail->getSubjectRendered());

        $mailTos = [];

        foreach ($mail->getTo() as $mail => $name) {
            if ($name) {
                $mailTos[] = sprintf('%s <%s>', $name, $mail);
            }
            else {
                $mailTos[] = $mail;
            }
        }

        $noteInstance->addData('recipient', 'text', implode(', ', $mailTos));

        foreach ($params as $key => $value) {
            if (is_string($value)) {
                $noteInstance->addData($key, 'text', $value);
            }
        }

        $this->noteService->storeNoteForEmail($noteInstance, $emailDocument);

        return true;
    }
}
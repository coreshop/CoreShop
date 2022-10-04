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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Note;
use Pimcore\Model\Tool\Email\Log;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class NoteService implements NoteServiceInterface
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getNoteById(int $id): ?Note
    {
        return Note::getById($id);
    }

    public function createPimcoreNoteInstance(Concrete $object, string $noteType): Note
    {
        $note = new Note();
        $note->setElement($object);
        $note->setDate(time());
        $note->setType($noteType);

        return $note;
    }

    public function createAnonymousNoteInstance(string $noteType): Note
    {
        $note = new Note();
        $note->setDate(time());
        $note->setType($noteType);

        return $note;
    }

    public function getObjectNotes(Concrete $object, string $noteType): array
    {
        $noteList = new Note\Listing();
        $noteList->addConditionParam('type = ?', $noteType);
        $noteList->addConditionParam('cid = ?', $object->getId());
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        return $noteList->load();
    }

    public function storeNoteForEmail(Note $note, Document\Email $emailDocument): Note
    {
        //Because logger does not return any id, we need to fetch the last one!
        /** @psalm-suppress InternalClass */
        $listing = new Log\Listing();
        $listing->addConditionParam('documentId = ?', $emailDocument->getId());
        $listing->setOrderKey('sentDate');
        $listing->setOrder('desc');
        $listing->setLimit(1);
        $logData = $listing->load();

        if (isset($logData[0]) && $logData[0] instanceof Log) {
            /** @psalm-suppress InternalMethod */
            $note->addData('email-log', 'text', $logData[0]->getId());
        }

        return $this->storeNote($note);
    }

    public function storeNote(Note $note, array $eventParams = []): Note
    {
        $note->save();

        $this->eventDispatcher->dispatch(
            new GenericEvent($note, $eventParams),
            sprintf('coreshop.note.%s.post_add', $note->getType()),
        );

        return $note;
    }

    public function deleteNote(int $noteId, array $eventParams = []): void
    {
        $note = $this->getNoteById($noteId);

        if (!$note instanceof Note) {
            return;
        }

        $noteType = $note->getType();

        if (method_exists($note, 'delete')) {
            $note->delete();
        }

        $this->eventDispatcher->dispatch(
            new GenericEvent($note, $eventParams),
            sprintf('coreshop.note.%s.pot_delete', $noteType),
        );
    }
}

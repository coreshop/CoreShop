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

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Note;
use Pimcore\Model\Tool\Email\Log;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class NoteService implements NoteServiceInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getNoteById($id)
    {
        return Note::getById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function createPimcoreNoteInstance(Concrete $object, $noteType)
    {
        $note = new Note();
        $note->setElement($object);
        $note->setDate(time());
        $note->setType($noteType);

        return $note;
    }

    /**
     * {@inheritdoc}
     */
    public function createAnonymousNoteInstance($noteType)
    {
        $note = new Note();
        $note->setDate(time());
        $note->setType($noteType);

        return $note;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectNotes(Concrete $object, $noteType)
    {
        $noteList = new Note\Listing();
        $noteList->addConditionParam('type = ?', $noteType);
        $noteList->addConditionParam('cid = ?', $object->getId());
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        return $noteList->load();
    }

    /**
     * {@inheritdoc}
     */
    public function storeNoteForEmail(Note $note, Document\Email $emailDocument)
    {
        //Because logger does not return any id, we need to fetch the last one!
        $listing = new Log\Listing();
        $listing->addConditionParam('documentId = ?', $emailDocument->getId());
        $listing->setOrderKey('sentDate');
        $listing->setOrder('desc');
        $listing->setLimit(1);
        $logData = $listing->load();

        if (isset($logData[0]) && $logData[0] instanceof Log) {
            $note->addData('email-log', 'text', $logData[0]->getId());
        }

        return $this->storeNote($note);
    }

    /**
     * {@inheritdoc}
     */
    public function storeNote(Note $note, $eventParams = [])
    {
        $note->save();

        $this->eventDispatcher->dispatch(
            sprintf('coreshop.note.%s.post_add', $note->getType()),
            new GenericEvent($note, $eventParams)
        );

        return $note;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteNote($noteId, $eventParams = [])
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
            sprintf('coreshop.note.%s.pot_delete', $noteType),
            new GenericEvent($note, $eventParams)
        );
    }
}

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

namespace CoreShop\Component\Pimcore\DataObject;

use Doctrine\DBAL\Exception\RetryableException;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Note;
use Pimcore\Model\Tool\Email\Log;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class NoteService implements NoteServiceInterface
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected LoggerInterface $pimcoreLogger,
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
        $maxRetries = 5;
        for ($retries = 0; $retries < $maxRetries; ++$retries) {
            try {
                $note->beginTransaction();
                $note->save();
                $note->commit();

                //the transaction was successfully completed, so we cancel the loop here -> no restart required
                break;
            } catch (\Exception $e) {
                try {
                    $note->rollBack();
                } catch (\Exception $er) {
                    // PDO adapter throws exceptions if rollback fails
                    $this->pimcoreLogger->info((string) $er);
                }

                if ($e instanceof RetryableException) {
                    // we try to start the transaction $maxRetries times again (deadlocks, ...)
                    if ($retries < ($maxRetries - 1)) {
                        $run = $retries + 1;
                        $waitTime = random_int(1, 5) * 100000; // microseconds
                        $this->pimcoreLogger->warning(
                            'Unable to finish transaction (' . $run . ". run) because of the following reason '" .
                            $e->getMessage() .
                            "'. --> Retrying in " . $waitTime . ' microseconds ... (' . ($run + 1) . ' of ' . $maxRetries . ')',
                        );

                        usleep($waitTime); // wait specified time until we restart the transaction
                    } else {
                        $this->pimcoreLogger->error(
                            'Finally giving up restarting the same transaction again and again, last message: ' . $e->getMessage(
                            ),
                        );

                        throw $e;
                    }

                    continue;
                }

                throw $e;
            }
        }

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

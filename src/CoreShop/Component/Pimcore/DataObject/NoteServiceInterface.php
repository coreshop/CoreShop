<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Note;

interface NoteServiceInterface
{
    public function getNoteById(int $id): ?Note;

    public function createPimcoreNoteInstance(Concrete $object, string $noteType): Note;

    public function createAnonymousNoteInstance(string $noteType): Note;

    /**
     * @return Note[]
     */
    public function getObjectNotes(Concrete $object, string $noteType): array;

    public function storeNoteForEmail(Note $note, Document\Email $emailDocument): Note;

    /**
     * @return Note
     */
    public function storeNote(Note $note, array $eventParams = []): ?Note;

    public function deleteNote(int $noteId, array $eventParams = []): void;
}

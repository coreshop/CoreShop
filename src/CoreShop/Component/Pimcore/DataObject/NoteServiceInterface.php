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

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

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Note;

interface NoteServiceInterface
{
    /**
     * @param int $id
     *
     * @return Note|null
     */
    public function getNoteById(int $id): ?Note;

    /**
     * @param Concrete $object
     * @param string   $noteType
     *
     * @return Note
     */
    public function createPimcoreNoteInstance(Concrete $object, string $noteType): Note;

    /**
     * @param string $noteType
     *
     * @return Note
     */
    public function createAnonymousNoteInstance(string $noteType): Note;

    /**
     * @param Concrete $object
     * @param string   $noteType
     *
     * @return Note[]
     */
    public function getObjectNotes(Concrete $object, string $noteType): array;

    /**
     * @param Note           $note
     * @param Document\Email $emailDocument
     *
     * @return Note
     */
    public function storeNoteForEmail(Note $note, Document\Email $emailDocument): Note;

    /**
     * @param Note  $note
     * @param array $eventParams
     *
     * @return Note
     */
    public function storeNote(Note $note, array $eventParams = []): ?Note;

    /**
     * @param int   $noteId
     * @param array $eventParams
     */
    public function deleteNote(int $noteId, array $eventParams = []): void;
}

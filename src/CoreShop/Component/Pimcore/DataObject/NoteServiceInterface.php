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

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Note;

interface NoteServiceInterface
{
    /**
     * @param int $id
     *
     * @return Note
     */
    public function getNoteById($id);

    /**
     * @param Concrete $object
     * @param string   $noteType
     *
     * @return Note
     */
    public function createPimcoreNoteInstance(Concrete $object, $noteType);

    /**
     * @param string $noteType
     *
     * @return Note
     */
    public function createAnonymousNoteInstance($noteType);

    /**
     * @param Concrete $object
     * @param string   $noteType
     *
     * @return mixed
     */
    public function getObjectNotes(Concrete $object, $noteType);

    /**
     * @param Note           $note
     * @param Document\Email $emailDocument
     *
     * @return Note
     */
    public function storeNoteForEmail(Note $note, Document\Email $emailDocument);

    /**
     * @param Note  $note
     * @param array $eventParams
     *
     * @return Note
     */
    public function storeNote(Note $note, $eventParams = []);

    /**
     * @param int   $noteId
     * @param array $eventParams
     */
    public function deleteNote($noteId, $eventParams = []);
}

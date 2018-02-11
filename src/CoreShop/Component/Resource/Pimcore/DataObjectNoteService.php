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

namespace CoreShop\Component\Resource\Pimcore;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Note;
use Pimcore\Model\Tool\Email\Log;

class DataObjectNoteService
{
    /**
     * @param $id
     * @return Note
     */
    public function getNoteById($id)
    {
        return Note::getById($id);
    }

    /**
     * @param PimcoreModelInterface $object
     * @param string                $noteType
     * @return Note
     */
    public function createPimcoreNoteInstance(PimcoreModelInterface $object, $noteType)
    {
        $note = new Note();
        $note->setElement($object);
        $note->setDate(time());
        $note->setType($noteType);

        return $note;
    }

    /**
     * @param string $noteType
     * @return Note
     */
    public function createAnonymousNoteInstance($noteType)
    {
        $note = new Note();
        $note->setDate(time());
        $note->setType($noteType);

        return $note;
    }

    /**
     * @param PimcoreModelInterface $object
     * @param string                $noteType
     * @return mixed
     */
    public function getObjectNotes(PimcoreModelInterface $object, $noteType)
    {
        $noteList = new Note\Listing();
        $noteList->addConditionParam('type = ?', $noteType);
        $noteList->addConditionParam('cid = ?', $object->getId());
        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        return $noteList->load();
    }

    /**
     * @param  Note           $note
     * @param  Document\Email $emailDocument
     * @return Note
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
     * @param $note
     * @return Note
     */
    public function storeNote(Note $note)
    {
        $note->save();
        return $note;
    }
}

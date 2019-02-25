<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\WorkflowBundle\History;

use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use Pimcore\Model\DataObject;
use Symfony\Component\Translation\TranslatorInterface;

class HistoryLogger implements HistoryLoggerInterface
{
    /**
     * @var NoteServiceInterface
     */
    private $noteService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $noteIdentifier;

    /**
     * @param NoteServiceInterface     $noteService
     * @param TranslatorInterface      $translator
     * @param string                   $noteIdentifier
     */
    public function __construct(
        NoteServiceInterface $noteService,
        TranslatorInterface $translator,
        $noteIdentifier
    ) {
        $this->noteService = $noteService;
        $this->translator = $translator;
        $this->noteIdentifier = $noteIdentifier;
    }

    /**
     * {@inheritdoc}
     */
    public function log(DataObject\Concrete $dataObject, $message = null, $description = null, $translate = false)
    {
        $note = $this->noteService->createPimcoreNoteInstance($dataObject, $this->noteIdentifier);

        $message = strip_tags($message);

        if ($translate === true) {
            $message = $this->translator->trans($message, [], 'admin');
        }

        $note->setTitle(
            sprintf('%s: %s',
                $this->translator->trans('coreshop_workflow_history_logger_prefix', [], 'admin'),
                $message)
        );

        if (null !== $description) {
            $note->setDescription($description);
        }

        $this->noteService->storeNote($note);
    }
}

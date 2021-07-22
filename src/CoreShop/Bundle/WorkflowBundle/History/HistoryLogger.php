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

namespace CoreShop\Bundle\WorkflowBundle\History;

use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use Pimcore\Model\DataObject;
use Symfony\Contracts\Translation\TranslatorInterface;

class HistoryLogger implements HistoryLoggerInterface
{
    private NoteServiceInterface $noteService;
    private TranslatorInterface $translator;
    private string $noteIdentifier;

    public function __construct(
        NoteServiceInterface $noteService,
        TranslatorInterface $translator,
        string $noteIdentifier
    ) {
        $this->noteService = $noteService;
        $this->translator = $translator;
        $this->noteIdentifier = $noteIdentifier;
    }

    public function log(
        DataObject\Concrete $object,
        ?string $message = null,
        ?string $description = null,
        bool $translate = false
    ): void
    {
        $note = $this->noteService->createPimcoreNoteInstance($object, $this->noteIdentifier);

        $message = strip_tags($message);

        if ($translate === true) {
            $message = $this->translator->trans($message, [], 'admin');
        }

        $note->setTitle(
            sprintf(
                '%s: %s',
                $this->translator->trans('coreshop_workflow_history_logger_prefix', [], 'admin'),
                $message
            )
        );

        if (null !== $description) {
            $note->setDescription($description);
        }

        $this->noteService->storeNote($note);
    }
}

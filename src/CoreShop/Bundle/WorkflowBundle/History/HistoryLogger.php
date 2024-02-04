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

namespace CoreShop\Bundle\WorkflowBundle\History;

use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use Pimcore\Model\DataObject;
use Symfony\Contracts\Translation\TranslatorInterface;

class HistoryLogger implements HistoryLoggerInterface
{
    public function __construct(
        private NoteServiceInterface $noteService,
        private TranslatorInterface $translator,
        private string $noteIdentifier,
    ) {
    }

    public function log(
        DataObject\Concrete $object,
        ?string $message = null,
        ?string $description = null,
        bool $translate = false,
    ): void {
        $note = $this->noteService->createPimcoreNoteInstance($object, $this->noteIdentifier);

        $message = strip_tags($message);

        if ($translate === true) {
            $message = $this->translator->trans($message, [], 'admin');
        }

        $note->setTitle('coreshop_history_change');
        $note->setDescription(
            sprintf(
                '%s: %s',
                $this->translator->trans('coreshop_workflow_history_logger_prefix', [], 'admin'),
                $message,
            ),
        );

        if (null !== $description) {
            $note->setDescription($description);
        }

        $this->noteService->storeNote($note);
    }
}

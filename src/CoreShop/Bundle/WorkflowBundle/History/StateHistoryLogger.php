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
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

final class StateHistoryLogger implements StateHistoryLoggerInterface
{
    public function __construct(
        private NoteServiceInterface $noteService,
        private TranslatorInterface $translator,
        private string $noteIdentifier,
    ) {
    }

    public function log(Concrete $object, Event $event): void
    {
        $transition = $event->getTransition();

        if (null === $transition) {
            return;
        }

        $from = $this->getFrom($transition->getFroms());
        $to = $this->getTo($transition->getTos());

        $fromValue = 'coreshop_workflow_state_' . $event->getWorkflowName() . '_' . $from;
        $toValue = 'coreshop_workflow_state_' . $event->getWorkflowName() . '_' . $to;

        $note = $this->noteService->createPimcoreNoteInstance($object, $this->noteIdentifier);
        $note->setTitle('coreshop_history_change');
        $note->setDescription(
            sprintf(
                '%s: %s %s %s %s',
                $this->translator->trans('coreshop_workflow_name_' . $event->getWorkflowName(), [], 'admin'),
                $this->translator->trans('coreshop_workflow_state_changed_from', [], 'admin'),
                $this->translator->trans($fromValue, [], 'admin'),
                $this->translator->trans('coreshop_workflow_state_changed_to', [], 'admin'),
                $this->translator->trans($toValue, [], 'admin'),
            ),
        );

        $note->addData('workflow', 'text', $event->getWorkflowName());
        $note->addData('transition', 'text', $transition->getName());

        $this->noteService->storeNote($note);
    }

    private function getFrom(array $froms)
    {
        return reset($froms);
    }

    private function getTo(array $tos)
    {
        return reset($tos);
    }
}

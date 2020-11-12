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

namespace CoreShop\Bundle\WorkflowBundle\History;

use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

final class StateHistoryLogger implements StateHistoryLoggerInterface
{
    private $noteService;
    private $translator;
    private $noteIdentifier;

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
    public function log(Concrete $object, Event $event): void
    {
        $transition = $event->getTransition();

        $from = $this->getFrom($transition->getFroms());
        $to = $this->getTo($transition->getTos());

        $fromValue = 'coreshop_workflow_state_' . $event->getWorkflowName() . '_' . $from;
        $toValue = 'coreshop_workflow_state_' . $event->getWorkflowName() . '_' . $to;

        $objectIdInfo = ' (Id ' . $object->getId() . ')';

        $note = $this->noteService->createPimcoreNoteInstance($object, $this->noteIdentifier);
        $note->setTitle(
            sprintf(
                '%s%s: %s %s %s %s',
                $this->translator->trans('coreshop_workflow_name_' . $event->getWorkflowName(), [], 'admin'),
                $objectIdInfo,
                $this->translator->trans('coreshop_workflow_state_changed_from', [], 'admin'),
                $this->translator->trans($fromValue, [], 'admin'),
                $this->translator->trans('coreshop_workflow_state_changed_to', [], 'admin'),
                $this->translator->trans($toValue, [], 'admin')
            )
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

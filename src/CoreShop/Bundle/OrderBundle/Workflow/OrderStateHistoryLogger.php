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

namespace CoreShop\Bundle\OrderBundle\Workflow;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use CoreShop\Component\Pimcore\DataObject\NoteServiceInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Workflow\Event\Event;

final class OrderStateHistoryLogger
{
    /**
     * @var StateMachineManager
     */
    private $stateMachineManager;

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
     * @param StateMachineManager $stateMachineManager
     * @param NoteServiceInterface $noteService
     * @param TranslatorInterface $translator
     * @param string $noteIdentifier
     */
    public function __construct(
        StateMachineManager $stateMachineManager,
        NoteServiceInterface $noteService,
        TranslatorInterface $translator,
        $noteIdentifier
    )
    {
        $this->stateMachineManager = $stateMachineManager;
        $this->noteService = $noteService;
        $this->translator = $translator;
        $this->noteIdentifier = $noteIdentifier;
    }

    /**
     * @param OrderInterface $order
     * @param Event $event
     */
    public function log(OrderInterface $order, Event $event)
    {
        $subject = $event->getSubject();
        $transition = $event->getTransition();

        $from = $this->getFrom($transition->getFroms());
        $to = $this->getTo($transition->getTos());

        $fromValue = 'coreshop_workflow_state_'.$event->getWorkflowName().'_'.$from;
        $toValue = 'coreshop_workflow_state_'.$event->getWorkflowName().'_'.$to;

        $objectIdInfo = '';
        // add id if it's not an order (since payment/shipping/invoice could be more than one)
        if (!$subject instanceof OrderInterface) {
            $objectIdInfo = ' (Id '.$subject->getId().')';
        }

        $note = $this->noteService->createPimcoreNoteInstance($order, $this->noteIdentifier);
        $note->setTitle(
            sprintf('%s%s: %s %s %s %s',
                $this->translator->trans('coreshop_workflow_name_'.$event->getWorkflowName(), [], 'admin'),
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

    /**
     * @param array $froms
     * @return mixed
     */
    private function getFrom(array $froms)
    {
        return reset($froms);
    }

    /**
     * @param array $tos
     * @return mixed
     */
    private function getTo(array $tos)
    {
        return reset($tos);
    }
}

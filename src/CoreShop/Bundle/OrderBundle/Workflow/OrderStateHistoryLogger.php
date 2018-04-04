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
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Resource\Pimcore\DataObjectNoteService;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManager;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Workflow\Event\Event;

final class OrderStateHistoryLogger
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var StateMachineManager
     */
    private $stateMachineManager;

    /**
     * @var DataObjectNoteService
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
     * @param OrderRepositoryInterface $orderRepository
     * @param StateMachineManager $stateMachineManager
     * @param DataObjectNoteService $noteService
     * @param TranslatorInterface $translator
     * @param string $noteIdentifier
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        StateMachineManager $stateMachineManager,
        DataObjectNoteService $noteService,
        TranslatorInterface $translator,
        $noteIdentifier
    )
    {
        $this->orderRepository = $orderRepository;
        $this->stateMachineManager = $stateMachineManager;
        $this->noteService = $noteService;
        $this->translator = $translator;
        $this->noteIdentifier = $noteIdentifier;
    }

    /**
     * @param Event $event
     * @param       $orderId
     */
    public function log($orderId = null, Event $event)
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order instanceof OrderInterface) {
            return;
        }

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

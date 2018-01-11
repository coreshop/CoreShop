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

namespace CoreShop\Bundle\OrderBundle\StateResolver;

use CoreShop\Component\Core\OrderPaymentStates;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\OrderStates;
use CoreShop\Component\Order\Processable\ProcessableInterface;
use CoreShop\Component\Order\StateResolver\StateResolverInterface;
use CoreShop\Component\Resource\Workflow\StateMachineManager;
use Symfony\Component\Workflow\Workflow;

final class OrderDocumentStateResolver implements StateResolverInterface
{
    /**
     * @var StateMachineManager
     */
    protected $stateMachineManager;

    /**
     * @var ProcessableInterface
     */
    protected $orderDocumentProcessableHelper;

    /**
     * @var string
     */
    protected $workflowIdentifier;

    /**
     * @var string
     */
    protected $transitionReady;

    /**
     * @var string
     */
    protected $transitionPartially;

    /**
     * @var string
     */
    protected $transitionCancel;

    /**
     * @var string
     */
    protected $transitionFully;

    /**
     * @param StateMachineManager $stateMachineManager
     * @param ProcessableInterface $orderDocumentProcessableHelper
     * @param string $workflowIdentifier
     * @param string $transitionReady
     * @param string $transitionPartially
     * @param string $transitionCancel
     * @param string $transitionFully
     */
    public function __construct(
        StateMachineManager $stateMachineManager,
        ProcessableInterface $orderDocumentProcessableHelper,
        string $workflowIdentifier,
        string $transitionReady,
        string $transitionPartially,
        string $transitionCancel,
        string $transitionFully
    )
    {
        $this->stateMachineManager = $stateMachineManager;
        $this->orderDocumentProcessableHelper = $orderDocumentProcessableHelper;
        $this->workflowIdentifier = $workflowIdentifier;
        $this->transitionReady = $transitionReady;
        $this->transitionPartially = $transitionPartially;
        $this->transitionCancel = $transitionCancel;
        $this->transitionFully = $transitionFully;
    }

    public function resolve(OrderInterface $order)
    {
        $workflow = $this->stateMachineManager->get($order, $this->workflowIdentifier);
        $targetTransition = $this->getTargetTransition($order);

        if (null !== $targetTransition) {
            $this->applyTransition($workflow, $order, $targetTransition);
        }
    }

    /**
     * @param Workflow $workflow
     * @param          $subject
     * @param string $transition
     */
    private function applyTransition(Workflow $workflow, $subject, string $transition)
    {
        if ($workflow->can($subject, $transition)) {
            $workflow->apply($subject, $transition);
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @return string|null
     */
    private function getTargetTransition(OrderInterface $order)
    {
        if (!$order->getPaymentState() === OrderPaymentStates::STATE_PAID) {
            return null;
        }

        if ($order->getOrderState() === OrderStates::STATE_CANCELLED) {
            return $this->transitionCancel;
        }

        if ($this->orderDocumentProcessableHelper->isFullyProcessed($order)) {
            return $this->transitionFully;
        }

        if (count($this->orderDocumentProcessableHelper->getProcessedItems($order)) === 0) {
            return $this->transitionReady;
        }

        return $this->transitionPartially;
    }
}
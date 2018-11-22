<?php

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;

final class OrderToOrderDocumentTransformerWorkflowApplier implements OrderDocumentTransformerInterface
{
    /**
     * @var OrderDocumentTransformerInterface
     */
    protected $innerTransformer;

    /**
     * @var StateMachineManagerInterface
     */
    protected $stateMachineManager;

    /**
     * @var string
     */
    protected $initialState;

    /**
     * @var string
     */
    protected $workflowName;

    /**
     * @var string
     */
    protected $transition;

    /**
     * @param OrderDocumentTransformerInterface $innerTransformer
     * @param StateMachineManagerInterface $stateMachineManager
     * @param string $initialState
     * @param string $workflowName
     * @param string $transition
     */
    public function __construct(OrderDocumentTransformerInterface $innerTransformer, StateMachineManagerInterface $stateMachineManager, string $initialState, string $workflowName, string $transition)
    {
        $this->innerTransformer = $innerTransformer;
        $this->stateMachineManager = $stateMachineManager;
        $this->initialState = $initialState;
        $this->workflowName = $workflowName;
        $this->transition = $transition;
    }

    public function transform(OrderInterface $order, OrderDocumentInterface $document, $items)
    {
        $document->setState($this->initialState);
        $document = $this->innerTransformer->transform($order, $document, $items);

        $workflow = $this->stateMachineManager->get($document, $this->workflowName);
        $workflow->apply($document, $this->transition);

        return $document;
    }
}
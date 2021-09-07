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

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;

final class OrderToOrderDocumentTransformerWorkflowApplier implements OrderDocumentTransformerInterface
{
    private OrderDocumentTransformerInterface $innerTransformer;
    private StateMachineManagerInterface $stateMachineManager;
    private string $initialState;
    private string $workflowName;
    private string $transition;

    public function __construct(
        OrderDocumentTransformerInterface $innerTransformer,
        StateMachineManagerInterface $stateMachineManager,
        string $initialState,
        string $workflowName,
        string $transition
    )
    {
        $this->innerTransformer = $innerTransformer;
        $this->stateMachineManager = $stateMachineManager;
        $this->initialState = $initialState;
        $this->workflowName = $workflowName;
        $this->transition = $transition;
    }

    public function transform(OrderInterface $order, OrderDocumentInterface $document, array $items): OrderDocumentInterface
    {
        $document->setState($this->initialState);
        $document = $this->innerTransformer->transform($order, $document, $items);

        $workflow = $this->stateMachineManager->get($document, $this->workflowName);
        $workflow->apply($document, $this->transition);

        return $document;
    }
}

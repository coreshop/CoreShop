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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;

final class OrderToOrderDocumentTransformerWorkflowApplier implements OrderDocumentTransformerInterface
{
    public function __construct(
        private OrderDocumentTransformerInterface $innerTransformer,
        private StateMachineManagerInterface $stateMachineManager,
        private string $initialState,
        private string $workflowName,
        private string $transition,
    ) {
    }

    public function transform(OrderInterface $order, OrderDocumentInterface $document, array $itemsToTransform): OrderDocumentInterface
    {
        $document->setState($this->initialState);
        $document = $this->innerTransformer->transform($order, $document, $itemsToTransform);

        $workflow = $this->stateMachineManager->get($document, $this->workflowName);
        $workflow->apply($document, $this->transition);

        return $document;
    }
}

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

use CoreShop\Bundle\WorkflowBundle\Manager\StateMachineManagerInterface;
use CoreShop\Component\Order\Model\OrderDocumentInterface;
use CoreShop\Component\Order\Model\OrderInterface;

final class OrderToOrderDocumentTransformerWorkflowApplier implements OrderDocumentTransformerInterface
{
    public function __construct(private OrderDocumentTransformerInterface $innerTransformer, private StateMachineManagerInterface $stateMachineManager, private string $initialState, private string $workflowName, private string $transition)
    {
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

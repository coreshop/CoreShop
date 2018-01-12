<?php

namespace CoreShop\Component\Core\Order\Transformer;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\OrderTransitions;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use CoreShop\Component\Resource\Workflow\StateMachineApplier;

final class CartToOrderTransformer implements ProposalTransformerInterface
{
    /**
     * @var ProposalTransformerInterface
     */
    protected $innerCartToOrderTransformer;

    /**
     * @var StateMachineApplier
     */
    protected $stateMachineApplier;

    /**
     * @param ProposalTransformerInterface $innerCartToOrderTransformer
     * @param StateMachineApplier          $stateMachineApplier
     */
    public function __construct(
        ProposalTransformerInterface $innerCartToOrderTransformer,
        StateMachineApplier $stateMachineApplier
    ) {
        $this->innerCartToOrderTransformer = $innerCartToOrderTransformer;
        $this->stateMachineApplier = $stateMachineApplier;
    }

    /**
     * @param ProposalInterface $cart
     * @param ProposalInterface $sale
     * @return ProposalInterface|mixed
     */
    public function transform(ProposalInterface $cart, ProposalInterface $sale)
    {
        $sale = $this->innerCartToOrderTransformer->transform($cart, $sale);

        if ($sale instanceof OrderInterface) {
            $this->stateMachineApplier->apply($sale, 'coreshop_order', OrderTransitions::TRANSITION_CREATE);
        }

        return $sale;
    }
}
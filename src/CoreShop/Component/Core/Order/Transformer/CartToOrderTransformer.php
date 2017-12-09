<?php

namespace CoreShop\Component\Core\Order\Transformer;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\QuoteInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use CoreShop\Component\Order\Workflow\WorkflowManagerInterface;
use Webmozart\Assert\Assert;

final class CartToOrderTransformer implements ProposalTransformerInterface
{
    /**
     * @var ProposalTransformerInterface
     */
    protected $innerCartToOrderTransformer;

    /**
     * @var WorkflowManagerInterface
     */
    protected $orderWorkflowManager;

    /**
     * @param ProposalTransformerInterface $innerCartToOrderTransformer
     * @param WorkflowManagerInterface $orderWorkflowManager
     */
    public function __construct(
        ProposalTransformerInterface $innerCartToOrderTransformer,
        WorkflowManagerInterface $orderWorkflowManager
    )
    {
        $this->innerCartToOrderTransformer = $innerCartToOrderTransformer;
        $this->orderWorkflowManager = $orderWorkflowManager;
    }

    public function transform(ProposalInterface $cart, ProposalInterface $sale)
    {
        $sale = $this->innerCartToOrderTransformer->transform($cart, $sale);

        if ($sale instanceof OrderInterface) {
            $this->orderWorkflowManager->changeState($sale, 'change_order_state', [
                'newState' => WorkflowManagerInterface::ORDER_STATE_INITIALIZED,
                'newStatus' => WorkflowManagerInterface::ORDER_STATE_INITIALIZED,
            ]);
        }

        return $sale;
    }
}
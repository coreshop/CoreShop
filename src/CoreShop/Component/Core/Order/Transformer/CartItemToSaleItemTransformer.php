<?php

namespace CoreShop\Component\Core\Order\Transformer;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\SaleItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Transformer\ProposalItemTransformerInterface;

final class CartItemToSaleItemTransformer implements ProposalItemTransformerInterface
{
    /**
     * @var ProposalItemTransformerInterface
     */
    private $innerCartItemToSaleItemTransformer;

    /**
     * @param ProposalItemTransformerInterface $innerCartItemToSaleItemTransformer
     */
    public function __construct(ProposalItemTransformerInterface $innerCartItemToSaleItemTransformer)
    {
        $this->innerCartItemToSaleItemTransformer = $innerCartItemToSaleItemTransformer;
    }

    /**
     * @param ProposalInterface     $proposal
     * @param ProposalItemInterface $fromProposalItem
     * @param ProposalItemInterface $toProposal
     *
     * @return mixed
     */
    public function transform(ProposalInterface $proposal, ProposalItemInterface $fromProposalItem, ProposalItemInterface $toProposal)
    {
        if ($fromProposalItem instanceof CartItemInterface && $toProposal instanceof SaleItemInterface) {
            $toProposal->setDigitalProduct($fromProposalItem->getDigitalProduct());
            $toProposal->setObjectId($fromProposalItem->getProduct()->getId());
            $mainObjectId = null;

            if ($fromProposalItem->getProduct() instanceof ProductInterface) {
                if ('variant' === $fromProposalItem->getProduct()->getType()) {
                    $mainProduct = $fromProposalItem->getProduct()->getVariantMaster();
                    $toProposal->setMainObjectId($mainProduct->getId());
                }
            }
        }

        return $this->innerCartItemToSaleItemTransformer->transform($proposal, $fromProposalItem, $toProposal);
    }
}

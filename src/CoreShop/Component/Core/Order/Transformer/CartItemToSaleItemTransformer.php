<?php

namespace CoreShop\Component\Core\Order\Transformer;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\QuoteInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Transformer\ProposalItemTransformerInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;
use Webmozart\Assert\Assert;

final class CartItemToSaleItemTransformer implements ProposalItemTransformerInterface
{
    /**
     * @var ProposalItemTransformerInterface
     */
    protected $innerCartItemToSaleItemTransformer;

    /**
     * @param ProposalItemTransformerInterface $innerCartItemToSaleItemTransformer
     */
    public function __construct(ProposalItemTransformerInterface $innerCartItemToSaleItemTransformer)
    {
        $this->innerCartItemToSaleItemTransformer = $innerCartItemToSaleItemTransformer;
    }

    public function transform(ProposalInterface $proposal, ProposalItemInterface $fromProposalItem, ProposalItemInterface $toProposal)
    {
        if ($fromProposalItem instanceof ProductInterface && $toProposal instanceof ProductInterface) {
            $toProposal->setDigitalProduct($fromProposalItem->getDigitalProduct());
        }

        return $this->innerCartItemToSaleItemTransformer->transform($proposal, $fromProposalItem, $toProposal);
    }
}
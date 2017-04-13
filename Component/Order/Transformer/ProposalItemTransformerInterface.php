<?php

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;

interface ProposalItemTransformerInterface {

    /**
     * Transforms one proposal item to another
     *
     * @param ProposalInterface $proposal
     * @param ProposalItemInterface $fromProposalItem
     * @param ProposalItemInterface $toProposal
     * @return mixed
     */
    public function transform(ProposalInterface $proposal, ProposalItemInterface $fromProposalItem, ProposalItemInterface $toProposal);

}
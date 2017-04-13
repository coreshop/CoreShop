<?php

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\ProposalInterface;

interface ProposalTransformerInterface {

    /**
     * Transforms one proposal to another
     *
     * @param ProposalInterface $fromProposal
     * @param ProposalInterface $toProposal
     * @return mixed
     */
    public function transform(ProposalInterface $fromProposal, ProposalInterface $toProposal);

}
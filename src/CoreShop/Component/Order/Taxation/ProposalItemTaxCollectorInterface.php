<?php

namespace CoreShop\Component\Order\Taxation;

use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;

interface ProposalItemTaxCollectorInterface
{
    /**
     * @param ProposalItemInterface $proposalItem
     * @return TaxItemInterface[]
     */
    public function getTaxes(ProposalItemInterface $proposalItem);
}
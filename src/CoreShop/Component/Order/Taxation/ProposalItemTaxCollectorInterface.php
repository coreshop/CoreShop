<?php

namespace CoreShop\Component\Order\Taxation;

use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Model\ProposalTaxItem;

interface ProposalItemTaxCollectorInterface
{
    /**
     * @param ProposalItemInterface $proposalItem
     * @return ProposalTaxItem[]
     */
    public function getTaxes(ProposalItemInterface $proposalItem);
}
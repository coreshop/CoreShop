<?php

namespace CoreShop\Component\Order\Taxation;

use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Taxation\Model\TaxItemInterface;

interface ProposalTaxCollectorInterface
{
    /**
     * @param ProposalInterface $proposal
     * @return TaxItemInterface[]
     */
    public function getTaxes(ProposalInterface $proposal);
}
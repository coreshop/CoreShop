<?php

namespace CoreShop\Component\Order\Taxation;

use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalTaxItem;
use CoreShop\Component\Taxation\Collector\TaxCollectorInterface;

interface ProposalTaxCollectorInterface
{
    /**
     * @param ProposalInterface $proposal
     * @return ProposalTaxItem[]
     */
    public function getTaxes(ProposalInterface $proposal);
}
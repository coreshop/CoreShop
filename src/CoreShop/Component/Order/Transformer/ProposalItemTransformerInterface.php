<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;

interface ProposalItemTransformerInterface
{
    /**
     * Transforms one proposal item to another.
     *
     * @param ProposalInterface     $proposal
     * @param ProposalItemInterface $fromProposalItem
     * @param ProposalItemInterface $toProposal
     *
     * @return mixed
     */
    public function transform(ProposalInterface $proposal, ProposalItemInterface $fromProposalItem, ProposalItemInterface $toProposal);
}

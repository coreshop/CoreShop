<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Order\Transformer;

use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\SaleItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Transformer\ProposalItemTransformerInterface;
use CoreShop\Component\Resource\Model\AbstractObject;

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
            $toProposal->setObjectId($fromProposalItem->getProduct()->getId());StoreProductUnitDefinitionPriceCalculator

            if ($fromProposalItem->getProduct() instanceof ProductInterface) {
                if ($fromProposalItem->getProduct()->getType() === AbstractObject::OBJECT_TYPE_VARIANT) {
                    $mainProduct = $fromProposalItem->getProduct()->getVariantMaster();
                    $toProposal->setMainObjectId($mainProduct->getId());
                }
            }

            if ($fromProposalItem->hasUnitDefinition()) {
                $unit = $fromProposalItem->getUnitDefinition()->getUnit();
                $toProposal->setUnitIdentifier($unit->getName());
                $toProposal->setUnit($unit->getId());
            }

            $toProposal->setItemWeight($fromProposalItem->getItemWeight());
            $toProposal->setTotalWeight($fromProposalItem->getTotalWeight());
        }

        return $this->innerCartItemToSaleItemTransformer->transform($proposal, $fromProposalItem, $toProposal);
    }
}

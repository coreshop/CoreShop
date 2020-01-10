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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\SaleInterface;
use CoreShop\Component\Order\Transformer\ProposalTransformerInterface;

abstract class AbstractSaleCreationController extends AbstractCartCreationController
{
    protected function persistCart(CartInterface $cart)
    {
        /**
         * @var SaleInterface $sale
         */
        $sale = $this->factory->createNew();
        $sale->setBackendCreated(true);
        $sale = $this->getTransformer()->transform($cart, $sale);
        $saleResponse = [
            'success' => true,
            'id' => $sale->getId(),
        ];
        $additionalResponse = $this->afterSaleCreation($sale);

        foreach ($additionalResponse as $key => $value) {
            $saleResponse[$key] = $value;
        }

        return $saleResponse;
    }

    /**
     * @return ProposalTransformerInterface
     */
    abstract protected function getTransformer();

    /**
     * @param ProposalInterface $sale
     * @returns array
     */
    abstract protected function afterSaleCreation(ProposalInterface $sale);
}

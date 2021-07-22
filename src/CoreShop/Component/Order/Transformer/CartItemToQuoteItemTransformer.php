<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Transformer;

use CoreShop\Component\Order\Model\CartItemInterface;
use CoreShop\Component\Order\Model\ProposalInterface;
use CoreShop\Component\Order\Model\ProposalItemInterface;
use CoreShop\Component\Order\Model\QuoteInterface;
use CoreShop\Component\Order\Model\QuoteItemInterface;
use Webmozart\Assert\Assert;

class CartItemToQuoteItemTransformer extends AbstractCartItemToSaleItemTransformer
{
    /**
     * {@inheritdoc}
     */
    public function transform(ProposalInterface $quote, ProposalItemInterface $cartItem, ProposalItemInterface $quoteItem)
    {
        /**
         * @var $quote     QuoteInterface
         * @var $cartItem  CartItemInterface
         * @var $quoteItem QuoteItemInterface
         */
        Assert::isInstanceOf($cartItem, CartItemInterface::class);
        Assert::isInstanceOf($quoteItem, QuoteItemInterface::class);
        Assert::isInstanceOf($quote, QuoteInterface::class);

        return $this->transformSaleItem($quote, $cartItem, $quoteItem, 'quote_item');
    }
}

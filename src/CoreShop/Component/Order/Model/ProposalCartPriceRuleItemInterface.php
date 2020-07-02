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

declare(strict_types=1);

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface ProposalCartPriceRuleItemInterface extends ResourceInterface
{
    /**
     * @return CartPriceRuleInterface
     */
    public function getCartPriceRule();

    /**
     * @param CartPriceRuleInterface $cartPriceRule
     */
    public function setCartPriceRule($cartPriceRule);

    /**
     * @return string
     */
    public function getVoucherCode();

    /**
     * @param string $voucherCode
     */
    public function setVoucherCode($voucherCode);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getDiscount(bool $withTax = true): int;

    /**
     * @param int  $discount
     * @param bool $withTax
     */
    public function setDiscount(int $discount, bool $withTax = true);
}

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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface CartPriceRuleVoucherCodeInterface extends ResourceInterface, TimestampableInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return bool
     */
    public function getUsed();

    /**
     * @param bool $used
     */
    public function setUsed($used);

    /**
     * @return int
     */
    public function getUses();

    /**
     * @param int $uses
     */
    public function setUses($uses);

    /**
     * @return bool
     */
    public function isCreditCode();

    /**
     * @param bool $isCreditCode
     */
    public function setIsCreditCode($isCreditCode);

    /**
     * @return int
     */
    public function getCreditAvailable();

    /**
     * @param int $creditAvailable
     */
    public function setCreditAvailable($creditAvailable);

    /**
     * @return CurrencyInterface|null
     */
    public function getCreditCurrency();

    /**
     * @param CurrencyInterface|null $creditCurrency
     */
    public function setCreditCurrency(?CurrencyInterface $creditCurrency);

    /**
     * @return int
     */
    public function getCreditUsed();

    /**
     * @param int $creditUsed
     */
    public function setCreditUsed($creditUsed);

    /**
     * @return CartPriceRuleInterface
     */
    public function getCartPriceRule();

    /**
     * @param CartPriceRuleInterface|null $cartPriceRule
     */
    public function setCartPriceRule($cartPriceRule = null);
}

<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;

interface CartPriceRuleVoucherCodeInterface extends ResourceInterface, TimestampableInterface
{
    public function getId(): ?int;

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

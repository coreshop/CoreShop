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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class CartPriceRuleVoucherCode implements CartPriceRuleVoucherCodeInterface
{
    use TimestampableTrait;
    use SetValuesTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var bool
     */
    protected $used;

    /**
     * @var int
     */
    protected $uses;

    /**
     * @var CartPriceRuleInterface
     */
    protected $cartPriceRule;

    /**
     * @var bool
     */
    protected $isCreditCode = false;

    /**
     * @var int
     */
    protected $creditAvailable = 0;

    /**
     * @var CurrencyInterface|null
     */
    protected $creditCurrency;

    /**
     * @var int
     */
    protected $creditUsed = 0;

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return void
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getUsed()
    {
        return $this->used;
    }

    /**
     * @return void
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }

    public function getUses()
    {
        return $this->uses;
    }

    /**
     * @return void
     */
    public function setUses($uses)
    {
        $this->uses = $uses;
    }

    public function isCreditCode()
    {
        return $this->isCreditCode;
    }

    /**
     * @return void
     */
    public function setIsCreditCode($isCreditCode)
    {
        $this->isCreditCode = $isCreditCode;
    }

    public function getCreditAvailable()
    {
        return $this->creditAvailable;
    }

    /**
     * @return void
     */
    public function setCreditAvailable($creditAvailable)
    {
        $this->creditAvailable = $creditAvailable;
    }

    public function getCreditCurrency()
    {
        return $this->creditCurrency;
    }

    /**
     * @return void
     */
    public function setCreditCurrency(?CurrencyInterface $creditCurrency)
    {
        $this->creditCurrency = $creditCurrency;
    }

    public function getCreditUsed()
    {
        return $this->creditUsed;
    }

    /**
     * @return void
     */
    public function setCreditUsed($creditUsed)
    {
        $this->creditUsed = $creditUsed;
    }

    public function getCartPriceRule()
    {
        return $this->cartPriceRule;
    }

    /**
     * @return void
     */
    public function setCartPriceRule($cartPriceRule = null)
    {
        $this->cartPriceRule = $cartPriceRule;
    }
}

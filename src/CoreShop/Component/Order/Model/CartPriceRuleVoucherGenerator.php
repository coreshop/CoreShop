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

/**
 * @psalm-suppress MissingConstructor
 */
class CartPriceRuleVoucherGenerator implements CartPriceRuleVoucherGeneratorInterface
{
    /**
     * @var int
     */
    protected $amount;

    /**
     * @var int
     */
    protected $length;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $suffix;

    /**
     * @var int
     */
    protected $hyphensOn;

    /**
     * @var CartPriceRuleInterface
     */
    protected $cartPriceRule;

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     *
     * @return void
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $length
     *
     * @return void
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return void
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     *
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     *
     * @return void
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * @return int
     */
    public function getHyphensOn()
    {
        return $this->hyphensOn;
    }

    /**
     * @param int $hyphensOn
     *
     * @return void
     */
    public function setHyphensOn($hyphensOn)
    {
        $this->hyphensOn = $hyphensOn;
    }

    /**
     * @return CartPriceRuleInterface
     */
    public function getCartPriceRule()
    {
        return $this->cartPriceRule;
    }

    /**
     * @param CartPriceRuleInterface $cartPriceRule
     *
     * @return void
     */
    public function setCartPriceRule($cartPriceRule)
    {
        $this->cartPriceRule = $cartPriceRule;
    }
}

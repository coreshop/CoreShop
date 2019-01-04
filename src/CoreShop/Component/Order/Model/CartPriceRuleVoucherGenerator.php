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

namespace CoreShop\Component\Order\Model;

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
     */
    public function setCartPriceRule($cartPriceRule)
    {
        $this->cartPriceRule = $cartPriceRule;
    }
}

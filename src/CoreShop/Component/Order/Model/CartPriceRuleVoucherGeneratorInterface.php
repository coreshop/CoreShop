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

interface CartPriceRuleVoucherGeneratorInterface
{
    /**
     * @return int|null
     */
    public function getAmount();

    /**
     * @param int $amount
     */
    public function setAmount($amount);

    /**
     * @return int|null
     */
    public function getLength();

    /**
     * @param int $length
     */
    public function setLength($length);

    /**
     * @return string
     */
    public function getFormat();

    /**
     * @param string $format
     */
    public function setFormat($format);

    /**
     * @return string
     */
    public function getPrefix();

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix);

    /**
     * @return string
     */
    public function getSuffix();

    /**
     * @param string $suffix
     */
    public function setSuffix($suffix);

    /**
     * @return int
     */
    public function getHyphensOn();

    /**
     * @param int $hyphensOn
     */
    public function setHyphensOn($hyphensOn);

    /**
     * @return CartPriceRuleInterface
     */
    public function getCartPriceRule();

    /**
     * @param CartPriceRuleInterface $cartPriceRule
     */
    public function setCartPriceRule($cartPriceRule);
}

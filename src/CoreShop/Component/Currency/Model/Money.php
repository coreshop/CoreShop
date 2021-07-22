<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Currency\Model;

class Money
{
    /**
     * @var int
     */
    public $value;

    /**
     * @var CurrencyInterface
     */
    public $currency;

    /**
     * @param int               $value
     * @param CurrencyInterface $currency
     */
    public function __construct(int $value, CurrencyInterface $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return CurrencyInterface
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param CurrencyInterface $currency
     */
    public function setCurrency(CurrencyInterface $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s%s', $this->value, $this->currency instanceof CurrencyInterface ? $this->currency->getIsoCode() : 'unknown currency');
    }
}

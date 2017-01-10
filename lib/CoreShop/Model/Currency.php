<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

/**
 * Class Currency
 * @package CoreShop\Model
 */
class Currency extends AbstractModel
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $isoCode;

    /**
     * @var int
     */
    public $numericIsoCode;

    /**
     * @var string
     */
    public $symbol;

    /**
     * @var float
     */
    public $exchangeRate;

    /**
     * Get Currency by name.
     *
     * @param $name
     *
     * @return Currency|null
     */
    public static function getByName($name)
    {
        return self::getByField("name", $name);
    }

    /**
     * Checks if currency is available.
     *
     * @return Currency[]
     */
    public static function getAvailable()
    {
        $countries = Country::getActiveCountries();

        $currencies = [];

        foreach ($countries as $c) {
            if ($c instanceof Country) {
                if (!array_key_exists($c->getCurrency()->getId(), $currencies)) {
                    $currencies[$c->getCurrency()->getId()] = $c->getCurrency();
                }
            }
        }

        return array_values($currencies);
    }

    /**
     * get if currency is active
     *
     * @return bool
     */
    public function getActive()
    {
        $countryList = Country::getList();
        $countryList->setCondition("active = 1 AND currencyId = ?", [$this->getId()]);
        return count($countryList->getData()) > 0;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s)", $this->getName(), $this->getId());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param mixed $isoCode
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;
    }

    /**
     * @return int
     */
    public function getNumericIsoCode()
    {
        return $this->numericIsoCode;
    }

    /**
     * @param int $numericIsoCode
     */
    public function setNumericIsoCode($numericIsoCode)
    {
        $this->numericIsoCode = $numericIsoCode;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @return float
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @param float $exchangeRate
     */
    public function setExchangeRate($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }
}

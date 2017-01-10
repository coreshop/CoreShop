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

use CoreShop\Exception;
use CoreShop\Model\Country\Dao;
use CoreShop\Model\User\Address;
use Pimcore\Placeholder;

/**
 * Class Country
 * @package CoreShop\Model
 */
class Country extends AbstractModel
{
    /**
     * @var bool
     */
    protected static $isMultiShop = true;

    /**
     * @var string
     */
    public $isoCode;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $active;

    /**
     * @var Currency
     */
    public $currency;

    /**
     * @var int
     */
    public $currencyId;

    /**
     * @var bool
     */
    public $useStoreCurrency;

    /**
     * @var int
     */
    public $zoneId;

    /**
     * @var Zone
     */
    public $zone;

    /**
     * @var string
     */
    public $addressFormat;

    /**
     * @var int[]
     */
    public $shopIds;

    /**
     * Get Currency by ISO-Code.
     *
     * @param $isoCode
     *
     * @return Country|null
     */
    public static function getByIsoCode($isoCode)
    {
        return static::getByField('isoCode', $isoCode);
    }

    /**
     * Gets all active Countries.
     *
     * @param int $shopId
     * @return array
     */
    public static function getActiveCountries($shopId = null)
    {
        if (is_null($shopId)) {
            $shopId = Shop::getDefaultShop()->getId();
        }

        $dao = new Dao();

        return $dao->getActiveCountries($shopId);
    }

    /**
     * @param Address $address
     * @param bool $asHtml
     * @return string
     */
    public function formatAddress(Address $address, $asHtml = true)
    {
        $objectVars = get_object_vars($address);

        $placeHolder = new Placeholder();
        $address = $placeHolder->replacePlaceholders($this->getAddressFormat(), $objectVars);

        if ($asHtml) {
            $address = nl2br($address);
        }

        return $address;
    }

    /**
     * @return mixed
     */
    public function getAddressFields()
    {
        $regex = "/" . Placeholder::getPlaceholderPrefix() . "([a-z_]+)\(([a-z_0-9]+)[\s,]*(.*?)\)" . Placeholder::getPlaceholderSuffix() . "/is";

        preg_match_all($regex, $this->getAddressFormat(), $matches);

        return $matches[2];
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
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param $isoCode
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;
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
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param $active
     */
    public function setActive($active)
    {
        if (is_bool($active)) {
            if ($active) {
                $active = 1;
            } else {
                $active = 0;
            }
        }
        $this->active = $active;
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        if ($this->getUseStoreCurrency()) {
            return \CoreShop::getTools()->getBaseCurrency();
        }

        if (!$this->currency instanceof Currency) {
            $this->currency = Currency::getById($this->currencyId);
        }

        return $this->currency;
    }

    /**
     * @param $currency
     *
     * @throws Exception
     */
    public function setCurrency($currency)
    {
        if (!$currency instanceof Currency) {
            throw new Exception('$currency must be instance of Currency');
        }

        $this->currency = $currency;
        $this->currencyId = $currency->getId();
    }

    /**
     * @return int
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * @param $currencyId
     *
     * @throws Exception
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
    }

    /**
     * @return Zone
     */
    public function getZone()
    {
        if (!$this->zone instanceof Zone) {
            $this->zone = Zone::getById($this->zoneId);
        }

        return $this->zone;
    }

    /**
     * @param $zone
     *
     * @throws Exception
     */
    public function setZone($zone)
    {
        if (!$zone instanceof Zone) {
            throw new Exception('$zone must be instance of Zone');
        }

        $this->zone = $zone;
        $this->zoneId = $zone->getId();
    }

    /**
     * @return int
     */
    public function getZoneId()
    {
        return $this->zoneId;
    }

    /**
     * @param $zoneId
     *
     * @throws Exception
     */
    public function setZoneId($zoneId)
    {
        $this->zoneId = $zoneId;
    }

    /**
     * @return bool
     */
    public function getUseStoreCurrency()
    {
        return $this->useStoreCurrency;
    }

    /**
     * @param bool $useStoreCurrency
     */
    public function setUseStoreCurrency($useStoreCurrency)
    {
        $this->useStoreCurrency = $useStoreCurrency;
    }

    /**
     * @return string
     */
    public function getAddressFormat()
    {
        return $this->addressFormat;
    }

    /**
     * @param string $addressFormat
     */
    public function setAddressFormat($addressFormat)
    {
        $this->addressFormat = $addressFormat;
    }

    /**
     * @return int[]
     */
    public function getShopIds()
    {
        return $this->shopIds;
    }

    /**
     * @param int[] $shopIds
     */
    public function setShopIds($shopIds)
    {
        $this->shopIds = $shopIds;
    }
}

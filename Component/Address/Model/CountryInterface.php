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
 * @copyright  Copyright (c) Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Address\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface CountryInterface
 * @package CoreShop\Component\Address\Model
 */
interface CountryInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getIsoCode();

    /**
     * @param $isoCode
     * @return static
     */
    public function setIsoCode($isoCode);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     * @return static
     */
    public function setName($name);

    /**
     * @return boolean
     */
    public function getActive();

    /**
     * @param boolean $active
     * @return static
     */
    public function setActive($active);

    /**
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @param CurrencyInterface $currency
     * @return static
     */
    public function setCurrency($currency);

    /**
     * @return ZoneInterface
     */
    public function getZone();

    /**
     * @param ZoneInterface $zone
     * @return static
     */
    public function setZone(ZoneInterface $zone = null);

    /**
     * @return string
     */
    public function getZoneName();

    /**
     * @return bool
     */
    public function getUseStoreCurrency();

    /**
     * @param bool $useStoreCurrency
     * @return static
     */
    public function setUseStoreCurrency($useStoreCurrency);

    /**
     * @return string
     */
    public function getAddressFormat();

    /**
     * @param string $addressFormat
     * @return static
     */
    public function setAddressFormat($addressFormat);

    /**
     * @return Collection|StoreInterface[]
     */
    public function getStores();

    /**
     * @return bool
     */
    public function hasStores();

    /**
     * @param StoreInterface $store
     */
    public function addStore(StoreInterface $store);

    /**
     * @param StoreInterface $store
     */
    public function removeStore(StoreInterface $store);

    /**
     * @param StoreInterface $store
     *
     * @return bool
     */
    public function hasStore(StoreInterface $store);
}
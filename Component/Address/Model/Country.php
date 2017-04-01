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

use CoreShop\Component\Resource\Model\AbstractResource;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Store
 * @package CoreShop\Component\Address\Model
 */
class Country implements CountryInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $isoCode;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $active;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var bool
     */
    protected $useStoreCurrency;

    /**
     * @var ZoneInterface
     */
    protected $zone;

    /**
     * @var string
     */
    protected $addressFormat;

     /**
     * @var Collection|CountryInterface[]
     */
    protected $stores;

    public function __construct()
    {
        $this->stores = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return static
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
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
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * @return static
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return CurrencyInterface
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param $currency
     * @return static
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
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
     * @return static
     */
    public function setUseStoreCurrency($useStoreCurrency)
    {
        $this->useStoreCurrency = $useStoreCurrency;

        return $this;
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
     * @return static
     */
    public function setAddressFormat($addressFormat)
    {
        $this->addressFormat = $addressFormat;

        return $this;
    }

    /**
     * @return ZoneInterface
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param ZoneInterface $zone
     * @return static
     */
    public function setZone(ZoneInterface $zone = null)
    {
        $this->zone = $zone;

        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * {@inheritdoc}
     */
    public function hasStores()
    {
        return !$this->stores->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addStore(StoreInterface $store)
    {
        if (!$this->hasStore($store)) {
            $this->stores->add($store);
            $store->setZone($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeStore(StoreInterface $store)
    {
        if ($this->hasStore($store)) {
            $this->stores->removeElement($store);
            $store->setZone(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasStore(StoreInterface $store)
    {
        return $this->stores->contains($store);
    }
}
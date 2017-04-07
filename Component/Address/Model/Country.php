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
 * Store.
 */
class Country extends AbstractResource implements CountryInterface
{
    /**
     * @var int
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
    protected $active = true;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var bool
     */
    protected $useStoreCurrency = true;

    /**
     * @var ZoneInterface
     */
    protected $zone;

    /**
     * @var string
     */
    protected $addressFormat = '';

    /**
     * @var Collection|StoreInterface[]
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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUseStoreCurrency()
    {
        return $this->useStoreCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function setUseStoreCurrency($useStoreCurrency)
    {
        $this->useStoreCurrency = $useStoreCurrency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddressFormat()
    {
        return $this->addressFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddressFormat($addressFormat)
    {
        $this->addressFormat = $addressFormat;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * {@inheritdoc}
     */
    public function setZone(ZoneInterface $zone = null)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getZoneName()
    {
        return $this->getZone() instanceof ZoneInterface ? $this->getZone()->getName() : '';
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
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeStore(StoreInterface $store)
    {
        if ($this->hasStore($store)) {
            $this->stores->removeElement($store);
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

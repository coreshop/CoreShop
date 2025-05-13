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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Address\Model\CountriesAwareTrait;
use CoreShop\Component\Store\Model\Store as BaseStore;
use Doctrine\Common\Collections\Collection;

/**
 * @psalm-suppress MissingConstructor
 */
class Store extends BaseStore implements StoreInterface
{
    use CountriesAwareTrait;

    /**
     * @var CountryInterface
     */
    private $baseCountry;

    /**
     * @var bool
     */
    protected $useGrossPrice = false;

    /**
     * @var Collection|ConfigurationInterface[]
     */
    protected $configurations;

    /**
     * @var Collection|CountryInterface[]
     */
    protected $countries;

    public function getConfigurations()
    {
        return $this->configurations;
    }

    public function hasConfigurations()
    {
        return !$this->configurations->isEmpty();
    }

    public function addConfiguration(ConfigurationInterface $configuration)
    {
        if (!$this->hasConfiguration($configuration)) {
            $this->configurations->add($configuration);
            $configuration->setStore($this);
        }
    }

    public function removeConfiguration(ConfigurationInterface $configuration)
    {
        if ($this->hasConfiguration($configuration)) {
            $this->configurations->removeElement($configuration);
            $configuration->setStore(null);
        }
    }

    public function hasConfiguration(ConfigurationInterface $configuration)
    {
        return $this->configurations->contains($configuration);
    }

    public function getBaseCountry()
    {
        return $this->baseCountry;
    }

    public function setBaseCountry(CountryInterface $baseCountry)
    {
        $this->baseCountry = $baseCountry;
    }

    /**
     * @return bool
     */
    public function getUseGrossPrice()
    {
        return $this->useGrossPrice;
    }

    /**
     * @param bool $useGrossPrice
     */
    public function setUseGrossPrice($useGrossPrice)
    {
        $this->useGrossPrice = $useGrossPrice;
    }
}

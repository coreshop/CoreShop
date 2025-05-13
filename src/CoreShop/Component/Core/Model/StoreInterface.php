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

use CoreShop\Component\Address\Model\CountriesAwareInterface;
use CoreShop\Component\Store\Model\StoreInterface as BaseStoreInterface;
use Doctrine\Common\Collections\Collection;

interface StoreInterface extends BaseStoreInterface, CountriesAwareInterface
{
    /**
     * @return bool
     */
    public function getUseGrossPrice();

    /**
     * @param bool $useGrossPrice
     */
    public function setUseGrossPrice($useGrossPrice);

    /**
     * @return CountryInterface
     */
    public function getBaseCountry();

    public function setBaseCountry(CountryInterface $baseCountry);

    /**
     * @return Collection|ConfigurationInterface[]
     */
    public function getConfigurations();

    /**
     * @return bool
     */
    public function hasConfigurations();

    public function addConfiguration(ConfigurationInterface $configuration);

    public function removeConfiguration(ConfigurationInterface $configuration);

    /**
     * @return bool
     */
    public function hasConfiguration(ConfigurationInterface $configuration);
}

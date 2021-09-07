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

declare(strict_types=1);

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

    /**
     * @param CountryInterface $baseCountry
     */
    public function setBaseCountry(CountryInterface $baseCountry);

    /**
     * @return Collection|ConfigurationInterface[]
     */
    public function getConfigurations();

    /**
     * @return bool
     */
    public function hasConfigurations();

    /**
     * @param ConfigurationInterface $configuration
     */
    public function addConfiguration(ConfigurationInterface $configuration);

    /**
     * @param ConfigurationInterface $configuration
     */
    public function removeConfiguration(ConfigurationInterface $configuration);

    /**
     * @param ConfigurationInterface $configuration
     *
     * @return bool
     */
    public function hasConfiguration(ConfigurationInterface $configuration);
}

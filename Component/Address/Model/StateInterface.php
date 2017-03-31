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

use CoreShop\Component\Core\Model\ResourceInterface;
use CoreShop\Component\Core\Model\MultishopInterface;
use CoreShop\Component\CurrencyBundle\Model\CurrencyInterface;

interface StateInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getIsoCode();

    /**
     * @param $isoCode
     */
    public function setIsoCode($isoCode);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     */
    public function setName($name);

    /**
     * @return int
     */
    public function getActive();

    /**
     * @param $active
     */
    public function setActive($active);

    /**
     * @return CountryInterface
     */
    public function getCountry();

    /**
     * @param CountryInterface $country
     */
    public function setCountry(CountryInterface $country);

    /**
     * @return int
     */
    public function getCountryId();

    /**
     * @param $countryId
     */
    public function setCountryId($countryId);
}
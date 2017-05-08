<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
*/

namespace CoreShop\Component\Address\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface ZoneInterface.
 */
interface ZoneInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     *
     * @return static
     */
    public function setName($name);

    /**
     * @return bool
     */
    public function getActive();

    /**
     * @param bool $active
     *
     * @return static
     */
    public function setActive($active);

    /**
     * @return Collection|CountryInterface[]
     */
    public function getCountries();

    /**
     * @return bool
     */
    public function hasCountries();

    /**
     * @param CountryInterface $country
     */
    public function addCountry(CountryInterface $country);

    /**
     * @param CountryInterface $country
     */
    public function removeCountry(CountryInterface $country);

    /**
     * @param CountryInterface $country
     *
     * @return bool
     */
    public function hasCountry(CountryInterface $country);
}

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

/**
 * Interface CountryInterface.
 */
interface CountryInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getIsoCode();

    /**
     * @param $isoCode
     *
     * @return static
     */
    public function setIsoCode($isoCode);

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
     * @return ZoneInterface
     */
    public function getZone();

    /**
     * @param ZoneInterface $zone
     *
     * @return static
     */
    public function setZone(ZoneInterface $zone = null);

    /**
     * @return string
     */
    public function getZoneName();

    /**
     * @return string
     */
    public function getAddressFormat();

    /**
     * @param string $addressFormat
     *
     * @return static
     */
    public function setAddressFormat($addressFormat);
}

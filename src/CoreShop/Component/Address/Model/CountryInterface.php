<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Address\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;

interface CountryInterface extends ResourceInterface, TranslatableInterface, TimestampableInterface, ToggleableInterface
{
    /**
     * @return string
     */
    public function getIsoCode();

    /**
     * @param string $isoCode
     */
    public function setIsoCode($isoCode);

    /**
     * @param string $language
     *
     * @return mixed
     */
    public function getName($language = null);

    /**
     * @param string $name
     * @param string $language
     *
     * @return mixed
     */
    public function setName($name, $language = null);

    /**
     * @return ZoneInterface
     */
    public function getZone();

    /**
     * @param ZoneInterface|null $zone
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
     */
    public function setAddressFormat($addressFormat);

    /**
     * @return array
     */
    public function getSalutations();

    /**
     * @param array $salutations
     */
    public function setSalutations($salutations);
}

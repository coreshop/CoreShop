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
 */

namespace CoreShop\Component\Address\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;

interface AddressInterface extends ResourceInterface, PimcoreModelInterface
{
    /**
     * @return string
     */
    public function getSalutation();

    /**
     * @param $salutation
     */
    public function setSalutation($salutation);

    /**
     * @return string
     */
    public function getFirstname();

    /**
     * @param $firstname
     */
    public function setFirstname($firstname);

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @param $lastname
     */
    public function setLastname($lastname);

    /**
     * @return string
     */
    public function getCompany();

    /**
     * @param $company
     */
    public function setCompany($company);

    /**
     * @return string
     */
    public function getStreet();

    /**
     * @param $street
     */
    public function setStreet($street);

    /**
     * @return string
     */
    public function getNumber();

    /**
     * @param $number
     */
    public function setNumber($number);

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @param $postcode
     */
    public function setPostcode($postcode);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param $city
     */
    public function setCity($city);

    /**
     * @return CountryInterface
     */
    public function getCountry();

    /**
     * @param $country
     */
    public function setCountry($country);

    /**
     * @return StateInterface
     */
    public function getState();

    /**
     * @param $state
     */
    public function setState($state);

    /**
     * @return string
     */
    public function getPhoneNumber();

    /**
     * @param $phoneNumber
     */
    public function setPhoneNumber($phoneNumber);
}

<?php

namespace CoreShop\Component\Address\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface AddressInterface extends ResourceInterface
{
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
    public function geCountry();

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

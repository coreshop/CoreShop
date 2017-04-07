<?php

namespace CoreShop\Component\Customer\Model;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;

interface CustomerInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getFirstname();

    /**
     * @param $firstname
     *
     * @return static
     */
    public function setFirstname($firstname);

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @param $lastname
     *
     * @return static
     */
    public function setLastname($lastname);

    /**
     * @return mixed
     */
    public function getGender();

    /**
     * @param $gender
     *
     * @return static
     */
    public function setGender($gender);

    /**
     * @return mixed
     */
    public function getEmail();

    /**
     * @param $email
     *
     * @return static
     */
    public function setEmail($email);

    /**
     * @return mixed
     */
    public function getPassword();

    /**
     * @param $password
     *
     * @return static
     */
    public function setPassword($password);

    /**
     * @return AddressInterface[]
     */
    public function getAddresses();

    /**
     * @param AddressInterface[] $addresses
     *
     * @return static
     */
    public function setAddresses($addresses);

    /**
     * @return bool
     */
    public function getIsGuest();

    /**
     * @param bool $guest
     *
     * @return static
     */
    public function setIsGuest($guest);

    /**
     * @return CustomerGroupInterface[]
     */
    public function getCustomerGroups();

    /**
     * @param CustomerGroupInterface[] $customerGroups
     *
     * @return static
     */
    public function setCustomerGroups($customerGroups);
}

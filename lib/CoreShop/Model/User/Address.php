<?php

namespace CoreShop\Model\User;

use CoreShop\Exception\UnsupportedException;
use Pimcore\Model\Object\Fieldcollection\Data\AbstractData;
use Pimcore\Model\Object\Fieldcollection\Data\CoreShopUserAddress;

class Address extends AbstractData
{
    /**
     * Create a new Instance.
     *
     * @return CoreShopUserAddress
     */
    public static function create()
    {
        return new CoreShopUserAddress();
    }

    /**
     * Get Firstname.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getFirstname()
    {
        throw new UnsupportedException('getFirstname is not supported for '.get_class($this));
    }

    /**
     * Set Firstname.
     *
     * @param string $firstname
     *
     * @throws UnsupportedException
     */
    public function setFirstname($firstname)
    {
        throw new UnsupportedException('setFirstname is not supported for '.get_class($this));
    }

    /**
     * Get Lastname.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getLastname()
    {
        throw new UnsupportedException('getLastname is not supported for '.get_class($this));
    }

    /**
     * Set Lastname.
     *
     * @param string $lastname
     *
     * @throws UnsupportedException
     */
    public function setLastname($lastname)
    {
        throw new UnsupportedException('setLastname is not supported for '.get_class($this));
    }

    /**
     * Get Company.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getCompany()
    {
        throw new UnsupportedException('getCompany is not supported for '.get_class($this));
    }

    /**
     * Set Company.
     *
     * @param string $company
     *
     * @throws UnsupportedException
     */
    public function setCompany($company)
    {
        throw new UnsupportedException('setCompany is not supported for '.get_class($this));
    }

    /**
     * Get Street.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getStreet()
    {
        throw new UnsupportedException('getStreet is not supported for '.get_class($this));
    }

    /**
     * Set Street.
     *
     * @param string $street
     *
     * @throws UnsupportedException
     */
    public function setStreet($street)
    {
        throw new UnsupportedException('setStreet is not supported for '.get_class($this));
    }

    /**
     * Get Number.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getNr()
    {
        throw new UnsupportedException('getNr is not supported for '.get_class($this));
    }

    /**
     * Set Number.
     *
     * @param string $nr
     *
     * @throws UnsupportedException
     */
    public function setNr($nr)
    {
        throw new UnsupportedException('setNr is not supported for '.get_class($this));
    }

    /**
     * Get ZIP.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getZip()
    {
        throw new UnsupportedException('getZip is not supported for '.get_class($this));
    }

    /**
     * Set ZIP.
     *
     * @param string $zip
     *
     * @throws UnsupportedException
     */
    public function setZip($zip)
    {
        throw new UnsupportedException('setZip is not supported for '.get_class($this));
    }

    /**
     * Get City.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getCity()
    {
        throw new UnsupportedException('getCity is not supported for '.get_class($this));
    }

    /**
     * Set City.
     *
     * @param string $city
     *
     * @throws UnsupportedException
     */
    public function setCity($city)
    {
        throw new UnsupportedException('setCity is not supported for '.get_class($this));
    }

    /**
     * Get Country.
     *
     * @throws UnsupportedException
     */
    public function getCountry()
    {
        throw new UnsupportedException('getCountry is not supported for '.get_class($this));
    }

    /**
     * Set Country.
     *
     * @param Country
     *
     * @throws UnsupportedException
     */
    public function setCountry($country)
    {
        throw new UnsupportedException('setCountry is not supported for '.get_class($this));
    }

    /**
     * Get State.
     *
     * @throws UnsupportedException
     */
    public function getState()
    {
        throw new UnsupportedException('getState is not supported for '.get_class($this));
    }

    /**
     * Set State.
     *
     * @param \CoreShop\Model\State $state
     *
     * @throws UnsupportedException
     */
    public function setState($state)
    {
        throw new UnsupportedException('setState is not supported for '.get_class($this));
    }

    /**
     * Get Extras.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getExtra()
    {
        throw new UnsupportedException('getExtra is not supported for '.get_class($this));
    }

    /**
     * Set Extras.
     *
     * @param string $extra
     *
     * @throws UnsupportedException
     */
    public function setExtra($extra)
    {
        throw new UnsupportedException('setExtra is not supported for '.get_class($this));
    }

    /**
     * Get Phone.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getPhone()
    {
        throw new UnsupportedException('getPhone is not supported for '.get_class($this));
    }

    /**
     * Set Phone.
     *
     * @param string $phone
     *
     * @throws UnsupportedException
     */
    public function setPhone($phone)
    {
        throw new UnsupportedException('setPhone is not supported for '.get_class($this));
    }

    /**
     * Get Phone Mobile.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getPhone_mobile()
    {
        throw new UnsupportedException('getPhone_mobile is not supported for '.get_class($this));
    }

    /**
     * Set Phone Mobile.
     *
     * @param string $phone_mobile
     *
     * @throws UnsupportedException
     */
    public function setPhone_mobile($phone_mobile)
    {
        throw new UnsupportedException('setPhone_mobile is not supported for '.get_class($this));
    }

    /**
     * Get Name.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getName()
    {
        throw new UnsupportedException('getName is not supported for '.get_class($this));
    }

    /**
     * Set Name.
     *
     * @param string $name
     *
     * @throws UnsupportedException
     */
    public function setName($name)
    {
        throw new UnsupportedException('setName is not supported for '.get_class($this));
    }

    /**
     * Get Vat Number.
     *
     * @return string
     *
     * @throws UnsupportedException
     */
    public function getVatNumber()
    {
        throw new UnsupportedException('getVatNumber is not supported for '.get_class($this));
    }

    /**
     * Set Vat Number.
     *
     * @param string $vatNumber
     *
     * @throws UnsupportedException
     */
    public function setVatNumber($vatNumber)
    {
        throw new UnsupportedException('setVatNumber is not supported for '.get_class($this));
    }
}

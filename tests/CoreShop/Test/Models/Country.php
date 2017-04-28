<?php

namespace CoreShop\Test\Models;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;
use Pimcore\Model\Object\Service;

class Country extends Base
{
    /**
     * Test Country Creation
     */
    public function testCountryCreation()
    {
        $this->printTestName();

        /**
         * @var $country CountryInterface
         */
        $country = $this->getFactory('country')->createNew();

        $country->setName('test-country');
        $country->setActive(true);
        $country->setIsoCode('TEC');
        $country->setZone($this->getRepository('zone')->find(1));

        $this->assertNull($country->getId());

        $this->getEntityManager()->persist($country);
        $this->getEntityManager()->flush();

        $this->assertNotNull($country->getId());
    }

    /**
     * Test Address Format
     */
    public function testAddressFormat() {
        $this->printTestName();

        /**
         * @var $address AddressInterface
         */
        $address = $this->getFactory('address')->createNew();
        $address->setCity('Wels');
        $address->setCountry(Data::$store->getBaseCountry());
        $address->setStreet('Freiung 9-11/N3');
        $address->setPostcode('4600');
        $address->setFirstname('Dominik');
        $address->setLastname('Pfaffenbauer');
        $address->setKey('test-address');
        $address->setParent(Service::createFolderByPath('/'));
        $address->save();

        $addressFormatted = $this->get('coreshop.address.formatter')->formatAddress($address, false);

        $this->assertSame(" \n Dominik Pfaffenbauer\nFreiung 9-11/N3 \n \nAustria ", $addressFormatted);
    }

    public function testCountryContext() {
        $this->printTestName();

        $this->assertEquals($this->get('coreshop.context.country')->getCountry()->getId(), Data::$store->getBaseCountry()->getId());
    }
}

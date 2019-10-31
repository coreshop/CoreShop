<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;
use Pimcore\Model\DataObject\Service;

class Country extends Base
{
    /**
     * Test Country Creation.
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
        $country->setSalutations(['mrs', 'mr']);

        $this->assertNull($country->getId());

        $this->getEntityManager()->persist($country);
        $this->getEntityManager()->flush();

        $this->assertNotNull($country->getId());
    }

    /**
     * Test Address Format.
     */
    public function testAddressFormat()
    {
        $this->printTestName();

        /**
         * @var AddressInterface
         */
        $address = $this->getFactory('address')->createNew();
        $address->setCity('Wels');
        $address->setCountry(Data::$store->getBaseCountry());
        $address->setStreet('Freiung 9-11/N3');
        $address->setPostcode('4600');
        $address->setSalutation('mr');
        $address->setFirstname('Dominik');
        $address->setLastname('Pfaffenbauer');
        $address->setKey('test-address' . uniqid());
        $address->setParent(Service::createFolderByPath('/'));
        $address->save();

        $addressFormatted = $this->get('coreshop.address.formatter')->formatAddress($address, false);

        $this->assertSame("\nMr. Dominik Pfaffenbauer\nFreiung 9-11/N3 \n\nAustria ", $addressFormatted);
    }

    public function testCountryContext()
    {
        $this->printTestName();

        $this->assertEquals($this->get('coreshop.context.country')->getCountry()->getId(), Data::$store->getBaseCountry()->getId());
    }
}

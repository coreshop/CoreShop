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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Pimcore\Model\DataObject\Folder;

final class AddressContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $addressFactory;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $addressRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $addressFactory
     * @param PimcoreRepositoryInterface $addressRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $addressFactory,
        PimcoreRepositoryInterface $addressRepository
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->addressFactory = $addressFactory;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @Given /^there is an address with (country "[^"]+"), "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     */
    public function thereIsAnAddress(CountryInterface $country, $postcode, $city, $street, $nr)
    {
        $this->createAddress($country, $postcode, $city, $street, $nr);
    }

    /**
     * @param CountryInterface $country
     * @param $postcode
     * @param $city
     * @param $street
     * @param $nr
     * @return AddressInterface
     */
    private function createAddress(CountryInterface $country, $postcode, $city, $street, $nr)
    {
        /**
         * @var AddressInterface $address
         */
        $address = $this->addressFactory->createNew();

        $address->setCountry($country);
        $address->setPostcode($postcode);
        $address->setCity($city);
        $address->setStreet($street);
        $address->setNumber($nr);
        $address->setKey(uniqid());
        $address->setPublished(true);
        $address->setParent(Folder::getByPath('/'));

        $this->saveAddress($address);

        return $address;
    }

    /**
     * @param AddressInterface $address
     */
    private function saveAddress(AddressInterface $address)
    {
        $address->save();

        $this->sharedStorage->set('address', $address);
    }
}

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

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Product\Model\ManufacturerInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\Persistence\ObjectManager;
use Pimcore\File;
use Pimcore\Model\DataObject\Service;

final class ManufacturerContext implements Context
{
    private $sharedStorage;
    private $manufacturerFactory;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $manufacturerFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->manufacturerFactory = $manufacturerFactory;
    }

    /**
     * @Given /^the site has a manufacturer "([^"]+)"$/
     */
    public function thereIsAManufacturer($name)
    {
        /**
         * @var ManufacturerInterface $manufacturer
         */
        $manufacturer = $this->manufacturerFactory->createNew();

        $manufacturer->setName($name);
        $manufacturer->setParent(Service::createFolderByPath('/manufacturer'));
        $manufacturer->setKey(File::getValidFilename($name));
        $manufacturer->save();

        $this->sharedStorage->set('manufacturer', $manufacturer);
    }
}

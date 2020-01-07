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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Component\Pimcore\DataObject\VersionHelper;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Version;

final class VersionContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage
    ) {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Then /^I remember the (product "[^"]+") Version$/
     * @Then /^I remember the (product) Version$/
     */
    public function iRememberTheProductVersion(Concrete $concrete)
    {
        $concrete->saveVersion();

        $this->sharedStorage->set('data_object_version_' . $concrete->getId(), $concrete->getLatestVersion(true)->getId());
    }

    /**
     * @Then /^I restore the remembered (product) Version$/
     */
    public function iRestoreTheRememberedProductVersion(Concrete $concrete)
    {
        $key = 'data_object_version_' . $concrete->getId();

        $data = $this->restoreVersion($concrete, $key);

        $this->sharedStorage->set('product-version', $concrete->getLatestVersion(true)->loadData());

        $data->save();

        $this->sharedStorage->set('product', $data);
    }

    /**
     * @Then /^I reset the restored Version$/
     */
    public function iResetTheRestoredVersion(Concrete $concrete)
    {
        $product = $this->sharedStorage->get('product');
        $id = $product->getId();

        $this->sharedStorage->set('product', $product::getById($id, true));
    }

    protected function restoreVersion(Concrete $concrete, $key)
    {
        if (!$this->sharedStorage->has($key)) {
            throw new \InvalidArgumentException('No Version remembered');
        }

        $version = Version::getById($this->sharedStorage->get($key));

        if (!$version) {
            throw new \InvalidArgumentException('Version does not exist');
        }

        $data = $version->loadData();

        if (!$data instanceof $concrete) {
            throw new \InvalidArgumentException('Version Type and Object Type do not match');
        }

        return $data;
    }
}

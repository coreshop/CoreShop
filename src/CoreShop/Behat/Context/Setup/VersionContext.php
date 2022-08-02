<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use Pimcore\Db;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Version;

final class VersionContext implements Context
{
    public function __construct(private SharedStorageInterface $sharedStorage)
    {
    }

    /**
     * @Then /^I remember the (product "[^"]+") Version$/
     * @Then /^I remember the (product) Version$/
     */
    public function iRememberTheProductVersion(Concrete $concrete): void
    {
        $concrete->saveVersion();

        $this->sharedStorage->set('data_object_version_' . $concrete->getId(), $concrete->getLatestVersion(true)->getId());
    }

    /**
     * @Then /^I restore the remembered (product) Version$/
     */
    public function iRestoreTheRememberedProductVersion(Concrete $concrete): void
    {
        $key = 'data_object_version_' . $concrete->getId();

        $data = $this->restoreVersion($concrete, $key);

        $GLOBALS['data'] = $data;

        $db = Db::get();
        $versionData = $db->fetchRow("SELECT id,date,versionCount FROM versions WHERE cid = ? AND ctype='object' ORDER BY `versionCount` DESC, `id` DESC LIMIT 1", $concrete->getId());
        $version = Version::getById($versionData['id']);

//        $version = $concrete->getLatestVersion();

        if (null === $version) {
            throw new \Exception('No Version found!');
        }

        $versionData = $version->loadData(false);

        $this->sharedStorage->set('product-version', $versionData);

        $data->save();

        $this->sharedStorage->set('product', $data);
    }

    /**
     * @Then /^I reset the restored Version$/
     */
    public function iResetTheRestoredVersion(Concrete $concrete): void
    {
        $product = $this->sharedStorage->get('product');
        $id = $product->getId();

        $this->sharedStorage->set('product', $product::getById($id, true));
    }

    private function restoreVersion(Concrete $concrete, string $key): Concrete
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

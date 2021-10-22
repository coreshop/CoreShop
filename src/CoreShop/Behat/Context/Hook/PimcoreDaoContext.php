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

namespace CoreShop\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Resource\Model\AbstractObject;
use Pimcore\Cache;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\User;
use Symfony\Component\HttpKernel\KernelInterface;

final class PimcoreDaoContext implements Context
{
    public function __construct(private KernelInterface $kernel, private OrderRepositoryInterface $orderRepository)
    {
    }

    /**
     * @BeforeScenario
     */
    public function setKernel(): void
    {
        \Pimcore::setKernel($this->kernel);
    }

    /**
     * @BeforeScenario
     */
    public function purgeObjects(): void
    {
        Cache::clearAll();
        Cache\Runtime::clear();

        /**
         * Delete Orders first, otherwise the CustomerDeletionListener would trigger.
         *
         * @var Listing $list
         */
        $list = $this->orderRepository->getList();
        $list->setUnpublished(true);
        $list->load();

        foreach ($list->getObjects() as $obj) {
            $obj->delete();
        }

        /**
         * @var Listing $list
         */
        $list = new DataObject\Listing();
        $list->setUnpublished(true);
        $list->setCondition('o_id <> 1');
        $list->load();

        foreach ($list->getObjects() as $obj) {
            $obj->delete();
        }
    }

    /**
     * @BeforeScenario
     */
    public function purgeBricks(): void
    {
        $list = new Objectbrick\Definition\Listing();
        $list->load();

        foreach ($list->load() as $brick) {
            if (!$brick instanceof Objectbrick\Definition) {
                continue;
            }

            if (str_starts_with($brick->getKey(), 'Behat')) {
                $brick->delete();
            }
        }
    }

    /**
     * @BeforeScenario
     */
    public function clearRuntimeCacheScenario(): void
    {
        //Clearing it here is totally fine, since each scenario has its own separated context of objects
        \Pimcore\Cache\Runtime::clear();
    }

    /**
     * @BeforeStep
     */
    public function clearRuntimeCacheStep(): void
    {
        //We should not clear Pimcore Objects here, otherwise we lose the reference to it
        //and end up having the same object twice
        $copy = \Pimcore\Cache\Runtime::getInstance()->getArrayCopy();
        $keepItems = [];

        foreach ($copy as $key => $value) {
            if (str_starts_with($key, 'object_')) {
                $keepItems[] = $key;
            }
        }

        \Pimcore\Cache\Runtime::clear($keepItems);
    }

    /**
     * @BeforeScenario
     */
    public function purgeClasses(): void
    {
        $list = new ClassDefinition\Listing();
        $list->setCondition('name LIKE ?', ['Behat%']);
        $list->load();

        foreach ($list->getClasses() as $class) {
            if (!$class instanceof ClassDefinition) {
                continue;
            }

            $class->delete();
        }
    }

    /**
     * @BeforeScenario
     */
    public function clearBehatAdminUser(): void
    {
        $user = User::getByName('behat-admin');

        if ($user) {
            $user->delete();
        }
    }

    /**
     * @BeforeScenario
     */
    public function disableGlobalInheritance(): void
    {
        AbstractObject::setGetInheritedValues(false);
    }

    /**
     * @BeforeScenario
     */
    public function purgeFieldCollections(): void
    {
        $list = new Fieldcollection\Definition\Listing();
        $list->load();

        foreach ($list->load() as $collection) {
            if (!$collection instanceof Fieldcollection\Definition) {
                continue;
            }

            if (str_starts_with($collection->getKey(), 'Behat')) {
                $collection->delete();
            }
        }
    }
}

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

namespace CoreShop\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Pimcore\Cache;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\DataObject\Objectbrick;

final class PimcoreDaoContext implements Context
{
    /**
     * @BeforeScenario
     */
    public function purgeObjects()
    {
        Cache::clearAll();
        Cache\Runtime::clear();

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
    public function purgeBricks()
    {
        $list = new Objectbrick\Definition\Listing();
        $list->load();

        foreach ($list->load() as $brick) {
            if (!$brick instanceof Objectbrick\Definition) {
                continue;
            }

            if (strpos($brick->getKey(), 'Behat') === 0) {
                $brick->delete();
            }
        }
    }

    /**
     * @BeforeScenario
     */
    public function clearRuntimeCacheScenario()
    {
        //Clearing it here is totally fine, since each scenario has its own separated context of objects
        \Pimcore\Cache\Runtime::clear();
    }

    /**
     * @BeforeStep
     */
    public function clearRuntimeCacheStep()
    {
        //We should not clear Pimcore Objects here, otherwise we lose the reference to it
        //and end up having the same object twice
        $copy = \Pimcore\Cache\Runtime::getInstance()->getArrayCopy();
        $keepItems = [];

        foreach ($copy as $key => $value) {
            if (strpos($key, 'object_') === 0) {
                $keepItems[] = $key;
            }
        }

        \Pimcore\Cache\Runtime::clear($keepItems);
    }

    /**
     * @BeforeScenario
     */
    public function purgeClasses()
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
    public function purgeFieldCollections()
    {
        $list = new Fieldcollection\Definition\Listing();
        $list->load();

        foreach ($list->load() as $collection) {
            if (!$collection instanceof Fieldcollection\Definition) {
                continue;
            }

            if (strpos($collection->getKey(), 'Behat') === 0) {
                $collection->delete();
            }
        }
    }
}

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

namespace CoreShop\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
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
        /**
         * @var $list Listing
         */
        $list = Concrete::getList();
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
    public function clearRuntimeCache()
    {
        \Pimcore\Cache\Runtime::clear();
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

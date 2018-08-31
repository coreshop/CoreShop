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
use Behat\Gherkin\Node\TableNode;
use CoreShop\Behat\Service\ClassStorageInterface;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Bundle\IndexBundle\Worker\MysqlWorker\TableIndex;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Product\Model\CategoryInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Pimcore\Model\DataObject\ClassDefinition;

final class FilterContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ClassStorageInterface
     */
    private $classStorage;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var FactoryInterface
     */
    private $filterFactory;

    /**
     * @var RepositoryInterface
     */
    private $filterRepository;

    /**
     * @var FactoryInterface
     */
    private $filterConditionFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ClassStorageInterface $classStorage
     * @param ObjectManager $objectManager
     * @param FactoryInterface $filterFactory
     * @param RepositoryInterface $filterRepository
     * @param FactoryInterface $filterConditionFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ClassStorageInterface $classStorage,
        ObjectManager $objectManager,
        FactoryInterface $filterFactory,
        RepositoryInterface $filterRepository,
        FactoryInterface $filterConditionFactory
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->classStorage = $classStorage;
        $this->objectManager = $objectManager;
        $this->filterFactory = $filterFactory;
        $this->filterRepository = $filterRepository;
        $this->filterConditionFactory = $filterConditionFactory;
    }

    /**
     * @Given /^the site has a filter "([^"]+)" for (index "[^"]+")$/
     */
    public function theSiteHasAFilterForIndex($name, IndexInterface $index)
    {
        $this->createFilter($name, $index);
    }

    /**
     * @Given /the (filter) has following conditions:/
     */
    public function theFilterHasFollowingConditions(FilterInterface $filter, TableNode $table)
    {
        $hash = $table->getHash();

        foreach ($hash as $row) {
            /**
             * @var $condition FilterConditionInterface
             */
            $condition = $this->filterConditionFactory->createNew();
            $condition->setType($row['type']);
            $condition->setConfiguration([
                'field' => $row['field']
            ]);
            $condition->setLabel($row['label']);

            $filter->addCondition($condition);

            $this->objectManager->persist($condition);
        }

        $this->saveFilter($filter);
    }

    /**
     * @Given /the (filter) has a category condition without category$/
     * @Given /the (filter) has a category condition with (category "[^"]+")$/
     * @Given /the (filter) has a category condition with (category "[^"]+") and it (includes all subcategories)$/
     */
    public function theFilterHasACategoryConditionWithCategory(FilterInterface $filter, CategoryInterface $category = null, $includeAllChilds = '')
    {
        $condition = $this->filterConditionFactory->createNew();
        $condition->setType('category_select');
        $condition->setConfiguration([
            'preSelect' => $category ? $category->getId() : null,
            'includeSubCategories' => $includeAllChilds === 'includes all subcategories'
        ]);
        $condition->setLabel('Category');

        $filter->addCondition($condition);

        $this->objectManager->persist($condition);

        $this->saveFilter($filter);
    }

    /**
     * @param                $name
     * @param IndexInterface $index
     */
    private function createFilter($name, IndexInterface $index)
    {
        /**
         * @var $filter FilterInterface
         */
        $filter = $this->filterFactory->createNew();
        $filter->setName($name);
        $filter->setIndex($index);

        $this->saveFilter($filter);
    }

    /**
     * @param FilterInterface $filter
     */
    private function saveFilter(FilterInterface $filter)
    {
        $this->objectManager->persist($filter);
        $this->objectManager->flush();

        $this->sharedStorage->set('filter', $filter);
    }
}

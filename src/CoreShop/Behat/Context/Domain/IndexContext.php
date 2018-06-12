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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pimcore\Model\DataObject\Concrete;
use Webmozart\Assert\Assert;

final class IndexContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $indexRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $indexRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $indexRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->sharedStorage = $sharedStorage;
        $this->indexRepository = $indexRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Then /^there should be a index "([^"]+)"$/
     */
    public function thereShouldBeAIndex($name)
    {
        $rates = $this->indexRepository->findBy(['name' => $name]);

        Assert::eq(
            count($rates),
            1,
            sprintf('%d indices has been found with name "%s".', count($rates), $name)
        );
    }

    /**
     * @Then /^the (index) should have columns "([^"]+)"$/
     */
    public function theIndexShouldHaveColumns(IndexInterface $index, $columns)
    {
        $columns = explode(', ', $columns);
        $tableName = sprintf('coreshop_index_mysql_%s', $index->getName());

        $this->indexShouldHaveColumnsInTable($tableName, $columns);
    }

    /**
     * @Then /^the (index) should have localized columns "([^"]+)"$/
     */
    public function theIndexShouldHaveLocalizedColumns(IndexInterface $index, $columns)
    {
        $columns = explode(', ', $columns);
        $tableName = sprintf('coreshop_index_mysql_localized_%s', $index->getName());

        $this->indexShouldHaveColumnsInTable($tableName, $columns);
    }

    /**
     * @Then /^the (index) should have indexed the (product "[^"]+")$/
     * @Then /^the (index) should have indexed the (object)$/
     */
    public function theIndexShouldHaveIndexedProduct(IndexInterface $index, IndexableInterface $object)
    {
        $productEntry = $this->fetchAllFromIndex($index, $object);

        Assert::isArray($productEntry, sprintf('Could not find index entry for object %s', $object->getId()));

        Assert::same(
            intval($productEntry['o_id']),
            $object->getId(),
            sprintf(
                'Expected to find id %s in index but found %s instead',
                intval($productEntry['o_id']),
                $object->getId()
            )
        );
    }

    /**
     * @Then /^the (index) should not have indexed the (object)$/
     * @Then /^the (index) should not have indexed the (product "[^"]+")$/
     */
    public function theIndexShouldNotHaveIndexedTheObject(IndexInterface $index, IndexableInterface $object)
    {
        $productEntry = $this->fetchAllFromIndex($index, $object);

        Assert::false($productEntry, sprintf('Could find index entry for object %s', $object->getId()));
    }

    /**
     * @Then /^the (index) column "([^"]+)" for (product "[^"]+") should have value "([^"]+)"$/
     * @Then /^the (index) column "([^"]+)" for (object-instance) should have value "([^"]+)"$/
     * @Then /^the (index) column "([^"]+)" for (object-instance "[^"]+") should have value "([^"]+)"$/
     */
    public function theIndexColumnForProductShouldHaveValue(IndexInterface $index, $column, IndexableInterface $object, $value)
    {
        $this->indexEntryShouldHaveValue($index, $object, $column, $value);
    }

    /**
     * @Then /^the (index) localized column "([^"]+)" for (product "[^"]+") should have value "([^"]+)"$/
     * @Then /^the (index) localized column "([^"]+)" for (object-instance) should have value "([^"]+)"$/
     * @Then /^the (index) localized column "([^"]+)" for (object-instance "[^"]+") should have value "([^"]+)"$/
     */
    public function theIndexLocalizedColumnForProductShouldHaveValue(IndexInterface $index, $column, IndexableInterface $object, $value)
    {
        $this->indexEntryShouldHaveValue($index, $object, $column, $value, true);
    }

    /**
     * @Then /^the (index) should have an index for "([^"]+)"$/
     */
    public function theIndexShouldHaveAnIndexFor(IndexInterface $index, $columns)
    {
        $columns = explode(', ', $columns);
        $tableName = sprintf('coreshop_index_mysql_%s', $index->getName());

        $this->indexShouldHaveIndexInTable($tableName, $columns);
    }

    /**
     * @Then /^the (index) should have an localized index for "([^"]+)"$/
     */
    public function theIndexShouldHaveAnLocalizedIndexFor(IndexInterface $index, $columns)
    {
        $columns = explode(', ', $columns);
        $tableName = sprintf('coreshop_index_mysql_localized_%s', $index->getName());

        $this->indexShouldHaveIndexInTable($tableName, $columns);
    }

    /**
     * @param IndexInterface $index
     * @param IndexableInterface $object
     * @param $column
     * @param $value
     * @param bool $localized
     */
    protected function indexEntryShouldHaveValue(IndexInterface $index, IndexableInterface $object, $column, $value, $localized = false)
    {
        $productEntry = $this->fetchAllFromIndex($index, $object, $localized);

        Assert::isArray($productEntry, sprintf('Could not find index entry for product %s', $object->getId()));
        Assert::keyExists($productEntry, $column, sprintf('Could not find column %s in index', $column));
        Assert::same(
            $productEntry[$column],
            $value,
            sprintf(
                'Expected column value %s for column %s to be %s',
                $productEntry[$column],
                $column,
                $value
            )
        );
    }

    /**
     * @param IndexInterface $index
     * @param IndexableInterface|null $object
     * @param bool $localized
     * @return array
     */
    protected function fetchAllFromIndex(IndexInterface $index, IndexableInterface $object = null, $localized = false)
    {
        if ($localized) {
            $tableName = sprintf('coreshop_index_mysql_localized_%s', $index->getName());
        } else {
            $tableName = sprintf('coreshop_index_mysql_%s', $index->getName());
        }


        if ($object instanceof Concrete) {
            if ($localized) {
                return $this->entityManager->getConnection()->fetchAssoc(sprintf('SELECT * FROM %s WHERE oo_id = %s', $tableName, $object->getId()));
            }

            return $this->entityManager->getConnection()->fetchAssoc(sprintf('SELECT * FROM %s WHERE o_id = %s', $tableName, $object->getId()));
        }

        return $this->entityManager->getConnection()->fetchAll(sprintf('SELECT * FROM %s', $tableName));
    }

    /**
     * @param string $tableName
     * @param array $columns
     */
    protected function indexShouldHaveColumnsInTable(string $tableName, array $columns)
    {
        $schemaManager = $this->entityManager->getConnection()->getSchemaManager();

        Assert::true($schemaManager->tablesExist([$tableName]), sprintf('Table with name %s should exist but was not found', $tableName));

        $tableColumns = $schemaManager->listTableColumns($tableName);

        foreach ($columns as $col) {
            $found = false;

            foreach ($tableColumns as $tableCol) {
                if ($tableCol->getName() === $col) {
                    $found = true;
                    break;
                }
            }

            Assert::true($found, sprintf('Table column %s not found', $col));
        }
    }

    /**
     * @param string $tableName
     * @param array $columns
     */
    protected function indexShouldHaveIndexInTable(string $tableName, array $columns)
    {
        $schemaManager = $this->entityManager->getConnection()->getSchemaManager();

        Assert::true($schemaManager->tablesExist([$tableName]), sprintf('Table with name %s should exist but was not found', $tableName));

        $table = $schemaManager->listTableDetails($tableName);
        $found = false;

        foreach ($table->getIndexes() as $index) {
            $found = true;

            foreach ($columns as $column) {
                if (!in_array($column, $index->getColumns())) {
                    $found = false;
                    break;
                }
            }

            if ($found) {
                break;
            }
        }

        Assert::true($found, sprintf('Index for columns %s not found', implode(', ', $columns)));
    }
}

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

namespace CoreShop\Bundle\IndexBundle\Worker;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Doctrine\DBAL\Connection;
use Pimcore\Tool;

abstract class AbstractListing implements ListingInterface
{
    protected IndexInterface $index;
    protected WorkerInterface $worker;
    protected Connection $connection;
    protected ?string $locale = null;

    public function __construct(IndexInterface $index, WorkerInterface $worker, Connection $connection)
    {
        $this->index = $index;
        $this->worker = $worker;
        $this->connection = $connection;
    }

    public function getIterator()
    {
        return $this;
    }

    abstract public function getObjects();

    abstract public function addCondition(ConditionInterface $condition, $fieldName);

    abstract public function addQueryCondition(ConditionInterface $condition, $fieldName);

    abstract public function addRelationCondition(ConditionInterface $condition, $fieldName);

    abstract public function resetCondition($fieldName);

    abstract public function resetQueryCondition($fieldName);

    abstract public function resetConditions();

    abstract public function setOrder($order);

    abstract public function getOrder();

    abstract public function setOrderKey($orderKey);

    abstract public function getOrderKey();

    abstract public function setLimit($limit);

    abstract public function getLimit();

    abstract public function setOffset($offset);

    abstract public function getOffset();

    abstract public function setCategory(PimcoreModelInterface $category);

    abstract public function getCategory();

    abstract public function setVariantMode($variantMode);

    abstract public function getVariantMode();

    abstract public function load(array $options = []);

    abstract public function getGroupByValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    abstract public function getGroupByRelationValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    abstract public function getGroupBySystemValues($fieldName, $countValues = false, $fieldNameShouldBeExcluded = true);

    abstract public function buildSimilarityOrderBy(array $fields, int $objectId): string;

    public function getIndex(): IndexInterface
    {
        return $this->index;
    }

    public function setIndex(IndexInterface $index): void
    {
        $this->index = $index;
    }

    public function getLocale(): ?string
    {
        //TODO: Use Locale Services
        if (is_null($this->locale)) {
            $this->locale = Tool::getDefaultLanguage();
        }

        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
}

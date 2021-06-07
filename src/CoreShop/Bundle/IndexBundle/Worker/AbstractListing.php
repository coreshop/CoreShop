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

namespace CoreShop\Bundle\IndexBundle\Worker;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Doctrine\DBAL\Connection;
use Exception;
use Pimcore\Tool;
use Traversable;

abstract class AbstractListing implements ListingInterface
{
    protected IndexInterface $index;
    protected WorkerInterface $worker;
    protected Connection $connection;
    protected string $locale;

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

    abstract public function buildSimilarityOrderBy($fields, $objectId);

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex(IndexInterface $index)
    {
        $this->index = $index;
    }

    public function getLocale()
    {
        //TODO: Use Locale Services
        if (is_null($this->locale)) {
            $language = null;

            if (!$language) {
                $language = Tool::getDefaultLanguage();
            }

            $this->locale = $language;
        }

        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}

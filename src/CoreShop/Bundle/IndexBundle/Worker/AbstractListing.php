<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\Worker;

use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Worker\WorkerInterface;
use Pimcore\Tool;

abstract class AbstractListing implements ListingInterface
{
    protected ?string $locale = null;

    public function __construct(
        protected IndexInterface $index,
        protected WorkerInterface $worker,
    ) {
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
        if (null === $this->locale) {
            $this->locale = Tool::getDefaultLanguage();
        }

        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
}

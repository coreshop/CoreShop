<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\TestBundle\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Component\Pimcore\BatchProcessing\DataObjectBatchListing;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Listing;
use Webmozart\Assert\Assert;

final class PimcoreContext implements Context
{
    /**
     * @Then /^iterating the (class|behat-class "[^"]+") should return (\d+) objects$/
     */
    public function iteratingTheClassShouldReturn(ClassDefinition $definition, int $count): void
    {
        $list = $this->getListingFromClassDefinition($definition);
        $batchListing = new DataObjectBatchListing($list, 1);

        Assert::eq($batchListing->count(), $count);
        Assert::eq($this->countBatchListingObjects($batchListing), $count);
    }

    /**
     * @Then /^iterating the (class|behat-class "[^"]+") with a offset of (\d+) should return (\d+) objects$/
     */
    public function iteratingTheClassWithAOffsetShouldReturn(ClassDefinition $definition, int $offset, int $count): void
    {
        $list = $this->getListingFromClassDefinition($definition);
        $list->setOffset($offset);

        $batchListing = new DataObjectBatchListing($list, 1);

        Assert::eq($batchListing->count(), $count);
        Assert::eq($this->countBatchListingObjects($batchListing), $count);
    }

    /**
     * @Then /^iterating the (class|behat-class "[^"]+") with a offset of (\d+) and limit of (\d+) should return (\d+) objects$/
     */
    public function iteratingTheClassWithAOffsetAndLimitShouldReturn(ClassDefinition $definition, int $offset, int $limit, int $count): void
    {
        $list = $this->getListingFromClassDefinition($definition);
        $list->setOffset($offset);
        $list->setLimit($limit);

        $batchListing = new DataObjectBatchListing($list, 1);

        Assert::eq($batchListing->count(), $count);
        Assert::eq($this->countBatchListingObjects($batchListing), $count);
    }

    private function countBatchListingObjects(DataObjectBatchListing $batchListing): int
    {
        $count = 0;

        foreach ($batchListing as $object) {
            ++$count;
        }

        return $count;
    }

    private function getListingFromClassDefinition(ClassDefinition $definition): Listing
    {
        /**
         * @psalm-var class-string $className
         */
        $className = sprintf('Pimcore\\Model\\DataObject\\%s\\Listing', $definition->getName());

        $list = new $className();

        /**
         * @Listing $list
         */
        Assert::isInstanceOf($list, Listing::class);

        return $list;
    }
}

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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Index\Factory\FilteredListingFactoryInterface;
use CoreShop\Component\Index\Filter\FilterProcessorInterface;
use CoreShop\Component\Index\Listing\ListingInterface;
use CoreShop\Component\Index\Listing\OrderAwareListingInterface;
use CoreShop\Component\Index\Model\FilterConditionInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Order\SimpleOrder;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Webmozart\Assert\Assert;

final class FilterContext implements Context
{
    private $sharedStorage;
    private $filterRepository;
    private $filterListFactory;
    private $filterProcessor;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $filterRepository,
        FilteredListingFactoryInterface $filterListFactory,
        FilterProcessorInterface $filterProcessor
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->filterRepository = $filterRepository;
        $this->filterListFactory = $filterListFactory;
        $this->filterProcessor = $filterProcessor;
    }

    /**
     * @Then /^there should be a filter with name "([^"]+)"$/
     */
    public function thereShouldBeAFilter($name)
    {
        $filters = $this->filterRepository->findBy(['name' => $name]);

        Assert::eq(
            count($filters),
            1,
            sprintf('%d Filters have been found with name "%s".', count($filters), $name)
        );
    }

    /**
     * @Then /^the (filter) should have (\d+) conditions$/
     */
    public function theFilterShouldHaveXConditions(FilterInterface $filter, $count)
    {
        Assert::eq(
            count($filter->getConditions()),
            $count,
            sprintf('%d Filters have been found with name "%s".', count($filter->getConditions()), $filter->getName())
        );
    }

    /**
     * @Then /the (filter) should have the values for (select) condition "([^"]+)":/
     * @Then /the (filter) should have the values for (multiselect) condition "([^"]+)":/
     */
    public function theFilterShouldHaveFollowingValuesForSelect(
        FilterInterface $filter,
        $conditionType,
        $field,
        TableNode $values
    ) {
        $conditions = $this->prepareFilter($filter);
        $shouldHaveConditions = [];

        foreach ($values as $value) {
            $shouldHaveConditions[] = $value['value'];
        }

        $field = reset(
            array_filter(
                $filter->getConditions()->toArray(),
                function (FilterConditionInterface $condition) use ($field) {
                    return $condition->getConfiguration()['field'] === $field;
                }
            )
        );

        Assert::isInstanceOf($field, FilterConditionInterface::class);
        Assert::eq($field->getType(), $conditionType);

        Assert::eq(count($conditions[$field->getId()]['values']), count($shouldHaveConditions));

        $values = array_map(
            function ($value) {
                return $value['value'];
            },
            $conditions[$field->getId()]['values']
        );

        $diff = array_diff($shouldHaveConditions, $values);

        Assert::count($diff, 0);
    }

    /**
     * @Then /the (filter) should have (\d+) values with count (\d+) for (relational_select) condition "([^"]+)"/
     * @Then /the (filter) should have (\d+) values with count (\d+) for (relational_multiselect) condition "([^"]+)"/
     */
    public function theFilterShouldHaveXValuesWithCountXForTypeAndField(
        FilterInterface $filter,
        $countOfValues,
        $countPerValue,
        $conditionType,
        $field
    ) {
        $conditions = $this->prepareFilter($filter);

        $field = reset(
            array_filter(
                $filter->getConditions()->toArray(),
                function (FilterConditionInterface $condition) use ($field) {
                    return $condition->getConfiguration()['field'] === $field;
                }
            )
        );

        Assert::isInstanceOf($field, FilterConditionInterface::class);
        Assert::eq($field->getType(), $conditionType);

        Assert::eq(count($conditions[$field->getId()]['values']), $countOfValues);

        $values = array_map(
            function ($value) {
                return $value['count'];
            },
            $conditions[$field->getId()]['values']
        );

        Assert::eq($values[0], $countPerValue);
    }

    /**
     * @Then /the (filter) should have (\d+) item(?:|s)$/
     */
    public function theFilterShouldHaveXItemsForCategoryCondition(FilterInterface $filter, $countOfValues)
    {
        $listing = $this->getFilterListing($filter);

        Assert::eq($listing->count(), $countOfValues);
    }

    /**
     * @Then /the (filter) should have (\d+) item(?:|s) with params:$/
     */
    public function theFilterShouldHaveXItemsForCategoryConditionWithParams(FilterInterface $filter, $countOfValues, TableNode $node)
    {
        $params = [];

        foreach ($node as $row) {
            $params[$row['key']] = $row['value'];
        }

        $listing = $this->getFilterListing($filter, $params);

        Assert::eq($listing->count(), $countOfValues);
    }

    /**
     * @Then /the (filter) should have (\d+) item(?:|s) for (manufacturer "[^"]+") in field "([^"]+)"$/
     * @Then /the (filter) should have (\d+) item(?:|s) for value "([^"]+)" in field "([^"]+)"$/
     */
    public function theFilterShouldHaveXItemsForCategoryWithObjectCondition(FilterInterface $filter, $countOfValues, $value, $field)
    {
        if ($value instanceof ResourceInterface) {
            $value = $value->getId();
        }

        if (strstr($field, '[]')) {
            $field = str_replace('[]', '', $field);

            if (strstr($value, ',')) {
                $value = explode(',', $value);
            } else {
                $value = [$value];
            }
        }

        $params = [
            $field => $value,
        ];

        $listing = $this->getFilterListing($filter, $params);

        Assert::eq($listing->count(), $countOfValues);
    }

    /**
     * @Then /^if I query the (filter) with a simple order for field "([^"]+)" and direction "([^"]+)" I should get two products "([^"]+)" and "([^"]+)"$/
     */
    public function ifIQueryWithASimpleOrder(FilterInterface $filter, $orderKey, $orderDir, $firstResult, $secondResult)
    {
        $filteredList = $this->filterListFactory->createList($filter, new ParameterBag());
        $filteredList->setLocale('en');

        if ($filteredList instanceof OrderAwareListingInterface) {
            $filteredList->addOrder(new SimpleOrder($orderKey, $orderDir));
        }

        $filteredList->load();
        $result = [];

        foreach ($filteredList->getObjects() as $object) {
            if (!$object instanceof IndexableInterface) {
                continue;
            }

            $result[] = $object->getIndexableName('en');
        }

        Assert::eq([$firstResult, $secondResult], $result);
    }

    /**
     * @param FilterInterface $filter
     * @param array           $filterParams
     *
     * @return array
     */
    private function prepareFilter(FilterInterface $filter, $filterParams = [])
    {
        $parameterBag = new ParameterBag($filterParams);

        $filteredList = $this->filterListFactory->createList($filter, $parameterBag);
        $filteredList->setLocale('en');
        $filteredList->setVariantMode(ListingInterface::VARIANT_MODE_HIDE);

        $currentFilter = $this->filterProcessor->processConditions($filter, $filteredList, $parameterBag);

        return $this->filterProcessor->prepareConditionsForRendering($filter, $filteredList, $currentFilter);
    }

    /**
     * @param FilterInterface $filter
     * @param array           $filterParams
     *
     * @return ListingInterface
     */
    private function getFilterListing(FilterInterface $filter, $filterParams = [])
    {
        $parameterBag = new ParameterBag($filterParams);

        $filteredList = $this->filterListFactory->createList($filter, $parameterBag);
        $filteredList->setLocale('en');
        $filteredList->setVariantMode(ListingInterface::VARIANT_MODE_HIDE);

        $this->filterProcessor->processConditions($filter, $filteredList, $parameterBag);

        return $filteredList;
    }
}

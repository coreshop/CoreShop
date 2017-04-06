<?php

namespace CoreShop\Bundle\IndexBundle\Factory;

use CoreShop\Component\Index\Factory\FilteredListingFactoryInterface;
use CoreShop\Component\Index\Factory\ListingFactoryInterface;
use CoreShop\Component\Index\Filter\FilterProcessorInterface;
use CoreShop\Component\Index\Model\FilterInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class FilteredListingFactory implements FilteredListingFactoryInterface {

    /**
     * @var ListingFactoryInterface
     */
    private $listingFactory;

    /**
     * @var FilterProcessorInterface
     */
    private $filterProcessor;

    /**
     * @param ListingFactoryInterface $listingFactory
     * @param FilterProcessorInterface $filterProcessor
     */
    public function __construct(ListingFactoryInterface $listingFactory, FilterProcessorInterface $filterProcessor)
    {
        $this->listingFactory = $listingFactory;
        $this->filterProcessor = $filterProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function createList(FilterInterface $filter, ParameterBag $parameterBag)
    {
        $list = $this->listingFactory->createList($filter->getIndex());

        $orderKey = $filter->getOrderKey();
        $orderDirection = $filter->getOrderDirection();
        $limit = $filter->getResultsPerPage();

        if ($parameterBag->has('orderKey')) {
            $orderKey = $parameterBag->get('orderKey');
        }

        if ($parameterBag->has('order')) {
            $orderDirection = $parameterBag->get('order');
        }

        if ($parameterBag->has('perPage')) {
            $limit = $parameterBag->get('perPage');
        }

        $list->setOrderKey($orderKey);
        $list->setOrder($orderDirection);
        $list->setLimit($limit);

        $this->filterProcessor->processConditions($filter, $list, $parameterBag);

        return $list;
    }
}
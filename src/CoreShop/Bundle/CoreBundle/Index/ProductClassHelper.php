<?php

namespace CoreShop\Bundle\CoreBundle\Index;

use CoreShop\Component\Core\Model\Product;
use CoreShop\Component\Index\ClassHelper\ClassHelperInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;

class ProductClassHelper implements ClassHelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSystemColumns()
    {
        return [
            'categoryIds' => IndexColumnInterface::FIELD_TYPE_STRING,
            'parentCategoryIds' => IndexColumnInterface::FIELD_TYPE_STRING,
            'shops' => IndexColumnInterface::FIELD_TYPE_STRING,
            'minPrice' => IndexColumnInterface::FIELD_TYPE_DOUBLE,
            'maxPrice' => IndexColumnInterface::FIELD_TYPE_DOUBLE,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizedSystemColumns()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexColumns(IndexableInterface $indexable)
    {
        if ($indexable instanceof Product) {
            $categoryIds = [];
            $parentCategoryIds = [];

            $categories = $indexable->getCategories();

            if ($categories) {
                foreach ($categories as $c) {
                    if ($c instanceof Product) {
                        $categoryIds[$c->getId()] = $c->getId();

                        $parents = $c->getHierarchy();

                        foreach ($parents as $p) {
                            $parentCategoryIds[] = $p->getId();
                        }
                    }
                }
            }

            return [
                'categoryIds' => implode(',', $categoryIds) . ',',
                'parentCategoryIds' => ',' . implode(',', $parentCategoryIds) . ',',
                'shops' => ','.@implode(',', $indexable->getStores()).','
            ];
        }

        return [];
    }
}
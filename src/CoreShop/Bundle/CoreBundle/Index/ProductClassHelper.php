<?php

namespace CoreShop\Bundle\CoreBundle\Index;

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
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
            'stores' => IndexColumnInterface::FIELD_TYPE_STRING,
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
        if ($indexable instanceof ProductInterface) {
            $categoryIds = [];
            $parentCategoryIds = [];

            $categories = $indexable->getCategories();

            if ($categories) {
                foreach ($categories as $c) {
                    if ($c instanceof CategoryInterface) {
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
                'stores' => ','.@implode(',', $indexable->getStores()).','
            ];
        }

        return [];
    }
}
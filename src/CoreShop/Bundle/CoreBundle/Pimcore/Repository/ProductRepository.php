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

namespace CoreShop\Bundle\CoreBundle\Pimcore\Repository;

use CoreShop\Bundle\ProductBundle\Pimcore\Repository\ProductRepository as BaseProductRepository;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductRepository extends BaseProductRepository implements ProductRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findLatestByStore(StoreInterface $store, $count = 8)
    {
        $conditions = [
            ['condition' => 'active = ?', 'variable' => 1],
            ['condition' => 'stores LIKE ?', 'variable' => '%,' . $store->getId() . ',%']
        ];

        return $this->findBy($conditions, ['o_creationDate DESC'], $count);
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts($options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'only_active'     => true,
            'categories'      => [],
            'store'           => null,
            'order_key'       => 'name',
            'order'           => 'ASC',
            'order_key_quote' => true,
            'object_types'    => null,
            'return_type'     => 'objects'
        ]);

        $resolver->setRequired(['store']);
        $resolver->setAllowedTypes('only_active', 'bool');
        $resolver->setAllowedTypes('categories', 'array');
        $resolver->setAllowedTypes('store', ['null', StoreInterface::class]);
        $resolver->setAllowedTypes('order_key', 'string');
        $resolver->setAllowedTypes('order_key_quote', 'bool');
        $resolver->setAllowedTypes('order', 'string');
        $resolver->setAllowedTypes('object_types', ['null', 'array']);
        $resolver->setAllowedTypes('return_type', 'string');
        $resolver->setAllowedValues('return_type', ['objects', 'list']);
        $resolver->setAllowedValues('object_types', function ($value) {
            $valid = [
                null,
                AbstractObject::OBJECT_TYPE_FOLDER,
                AbstractObject::OBJECT_TYPE_OBJECT,
                AbstractObject::OBJECT_TYPE_VARIANT
            ];
            $isvalid = $value === null || !array_diff($value, $valid);
            return $isvalid;
        });

        $listOptions = $resolver->resolve($options);

        $list = $this->getList();

        if ($listOptions['object_types'] !== null) {
            $list->setObjectTypes($listOptions['object_types']);
        }

        if ($listOptions['only_active'] === true) {
            $list->addConditionParam('active = ?', 1);
        }

        $classId = $list->getClassId();
        if (count($listOptions['categories']) > 0) {
            $categoryIds = [];
            foreach ($listOptions['categories'] as $category) {
                if ($category instanceof CategoryInterface) {
                    $categoryIds[] = $category->getId();
                }
            }
            if (count($categoryIds) > 0) {
                $list->addConditionParam('(o_id IN (SELECT DISTINCT src_id FROM object_relations_' . $classId . ' WHERE fieldname = "categories" AND dest_id IN (' . join(',', $categoryIds) . ')))');
            }
        }

        if ($listOptions['store'] instanceof StoreInterface) {
            $list->addConditionParam('stores LIKE ?', '%,' . $listOptions['store']->getId() . ',%');
        }

        $list->setOrderKey($listOptions['order_key'], $listOptions['order_key_quote']);
        $list->setOrder($listOptions['order']);

        return $listOptions['return_type'] === 'objects' ? $list->load() : $list;
    }
}
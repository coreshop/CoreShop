<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Form\Type\Product;

use CoreShop\Bundle\ProductBundle\Form\Type\ProductSelectionType;
use CoreShop\Bundle\ProductBundle\Form\Type\Unit\ProductUnitDefinitionPriceCollectionType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ProductStoreValuesType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);

        $builder
            ->add('store', StoreChoiceType::class)
            ->add('product', ProductSelectionType::class)
            ->add('price', IntegerType::class)
            ->add('productUnitDefinitionPrices', ProductUnitDefinitionPriceCollectionType::class);
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $event->setData($this->parseStorePostData($data));
    }

    /**
     * @param FormEvent $event
     */
    public function onPostSubmit(FormEvent $event)
    {
        /** @var ProductStoreValuesInterface $data */
        $data = $event->getData();
        if ($data->getPrice() >= PHP_INT_MAX) {
            $event->getForm()->addError(new FormError('Value exceeds PHP_INT_MAX please use an input data type instead of numeric!'));
        }
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function parseStorePostData(array $data)
    {
        $storeId = $data['storeId'];
        $objectId = $data['objectId'];

        $price = null;
        $defaultUnitDefinition = null;

        if ($data['price'] !== null) {
            $price = (int) round((round($data['price'], 2) * 100), 0);
        }

        $productUnitDefinitionPrices = [];
        if (is_array($data['productUnitDefinitionPrices'])) {
            foreach ($data['productUnitDefinitionPrices'] as $unitDefinitionPrice) {
                $productUnitDefinitionPrices[] = $unitDefinitionPrice;
            }
        }

        return [
            'store'                       => $storeId,
            'product'                     => $objectId,
            'price'                       => $price,
            'productUnitDefinitionPrices' => $productUnitDefinitionPrices
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_store_values';
    }
}

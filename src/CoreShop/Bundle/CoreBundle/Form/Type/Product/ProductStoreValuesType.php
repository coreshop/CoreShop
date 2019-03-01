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
use CoreShop\Bundle\ProductBundle\Form\Type\Unit\ProductUnitChoiceType;
use CoreShop\Bundle\ProductQuantityPriceRulesBundle\Form\Type\Unit\ProductAdditionalUnitCollectionType;
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
     * @param string $dataClass
     * @param array  $validationGroups
     */
    public function __construct($dataClass, array $validationGroups)
    {
        parent::__construct($dataClass, $validationGroups);
    }

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
            ->add('defaultUnit', ProductUnitChoiceType::class)
            ->add('defaultUnitPrecision', IntegerType::class)
            ->add('additionalUnits', ProductAdditionalUnitCollectionType::class);
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
     * @param array $parsedData
     *
     * @return array
     */
    protected function parseStorePostData(array $parsedData)
    {
        $storeId = $parsedData['storeId'];
        $objectId = $parsedData['objectId'];

        $price = null;
        $defaultUnit = null;
        $defaultUnitPrecision = 0;

        if ($parsedData['price'] !== null) {
            $price = (int) round((round($parsedData['price'], 2) * 100), 0);
        }

        if (is_numeric($parsedData['defaultUnit'])) {
            $defaultUnit = (int) $parsedData['defaultUnit'];
        }

        if (is_numeric($parsedData['defaultUnitPrecision'])) {
            $defaultUnitPrecision = (int) $parsedData['defaultUnitPrecision'];
        }

        $additionalUnits = [];
        if (is_array($parsedData['additionalUnit'])) {
            foreach ($parsedData['additionalUnit'] as $additionalUnit) {
                $productAwareAdditionalUnit = $additionalUnit;
                $productAwareAdditionalUnit['product'] = $objectId;
                $additionalUnits[] = $productAwareAdditionalUnit;
            }
        }

        return [
            'store'                => $storeId,
            'product'              => $objectId,
            'defaultUnit'          => $defaultUnit,
            'price'                => $price,
            'defaultUnitPrecision' => $defaultUnitPrecision,
            'additionalUnits'      => $additionalUnits
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

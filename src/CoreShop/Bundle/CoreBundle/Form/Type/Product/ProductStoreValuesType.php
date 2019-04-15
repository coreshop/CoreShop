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

use CoreShop\Bundle\MoneyBundle\Form\Type\MoneyType;
use CoreShop\Bundle\ProductBundle\Form\Type\Unit\ProductUnitDefinitionPriceCollectionType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
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
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);

        $builder
            ->add('store', StoreChoiceType::class)
            ->add('price', MoneyType::class)
            ->add('productUnitDefinitionPrices', ProductUnitDefinitionPriceCollectionType::class);
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
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_store_values';
    }
}

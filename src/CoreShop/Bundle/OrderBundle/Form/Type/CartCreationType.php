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

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerSelectionType;
use CoreShop\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use CoreShop\Component\Order\OrderSaleStates;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CartCreationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
            ])
            ->add('customer', CustomerSelectionType::class)
            ->add('store', StoreChoiceType::class, [
                'label' => 'coreshop_store',
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'label' => 'coreshop_currency',
            ])
            ->add('localeCode', LocaleChoiceType::class, [
                'label' => 'coreshop_locale',
            ])
            ->add('items', CollectionType::class, [
                'entry_type' => CartCreationCartItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'csrf_protection' => false,
            'customer' => null,
            'sales_state' => OrderSaleStates::STATE_CART,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_cart_creation';
    }
}

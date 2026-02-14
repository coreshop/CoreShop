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

namespace CoreShop\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class StoreSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Base
            ->add('guest_checkout', CheckboxType::class, [
                'label' => 'coreshop_guestcheckout',
                'required' => false,
                'priority' => 1000,
            ])
            // Category
            ->add('category_list_mode', ChoiceType::class, [
                'label' => 'coreshop_category_list_mode',
                'choices' => [
                    'List' => 'list',
                    'Grid' => 'grid',
                ],
                'required' => false,
                'priority' => 900,
            ])
            ->add('category_list_per_page', TextType::class, [
                'label' => 'coreshop_category_list_per_page',
                'required' => false,
                'priority' => 890,
            ])
            ->add('category_list_per_page_default', IntegerType::class, [
                'label' => 'coreshop_category_list_per_page_default',
                'required' => false,
                'priority' => 880,
            ])
            ->add('category_list_include_subcategories', CheckboxType::class, [
                'label' => 'coreshop_category_list_include_subcategories',
                'required' => false,
                'priority' => 870,
            ])
            ->add('category_grid_per_page', TextType::class, [
                'label' => 'coreshop_category_grid_per_page',
                'required' => false,
                'priority' => 860,
            ])
            ->add('category_grid_per_page_default', IntegerType::class, [
                'label' => 'coreshop_category_grid_per_page_default',
                'required' => false,
                'priority' => 850,
            ])
            ->add('category_variant_mode', ChoiceType::class, [
                'label' => 'coreshop_category_variant_mode',
                'choices' => [
                    'Hide' => 'hide',
                    'Include' => 'include',
                    'Include Parent Object' => 'include_parent_object',
                ],
                'required' => false,
                'priority' => 840,
            ])
            // Quote
            ->add('quote_prefix', TextType::class, [
                'label' => 'coreshop_prefix',
                'required' => false,
                'priority' => 700,
            ])
            ->add('quote_suffix', TextType::class, [
                'label' => 'coreshop_suffix',
                'required' => false,
                'priority' => 690,
            ])
            // Order
            ->add('order_prefix', TextType::class, [
                'label' => 'coreshop_prefix',
                'required' => false,
                'priority' => 600,
            ])
            ->add('order_suffix', TextType::class, [
                'label' => 'coreshop_suffix',
                'required' => false,
                'priority' => 590,
            ])
            // Invoice
            ->add('invoice_prefix', TextType::class, [
                'label' => 'coreshop_prefix',
                'required' => false,
                'priority' => 500,
            ])
            ->add('invoice_suffix', TextType::class, [
                'label' => 'coreshop_suffix',
                'required' => false,
                'priority' => 490,
            ])
            ->add('invoice_wkhtml', TextType::class, [
                'label' => 'coreshop_wkhtmltopdf_params',
                'required' => false,
                'priority' => 480,
            ])
            // Shipment
            ->add('shipment_prefix', TextType::class, [
                'label' => 'coreshop_prefix',
                'required' => false,
                'priority' => 400,
            ])
            ->add('shipment_suffix', TextType::class, [
                'label' => 'coreshop_suffix',
                'required' => false,
                'priority' => 390,
            ])
            ->add('shipment_wkhtml', TextType::class, [
                'label' => 'coreshop_wkhtmltopdf_params',
                'required' => false,
                'priority' => 380,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_store_settings';
    }
}

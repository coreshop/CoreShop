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

namespace CoreShop\Bundle\ShippingBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\PimcoreAssetChoiceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CarrierType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class, [
                'label' => 'coreshop_identifier',
                'priority' => 100,
            ])
            ->add('trackingUrl', TextType::class, [
                'label' => 'coreshop_carrier_trackingUrl',
                'priority' => 90,
            ])
            ->add('logo', PimcoreAssetChoiceType::class, [
                'label' => 'coreshop_logo',
                'priority' => 80,
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => CarrierTranslationType::class,
                'priority' => 70,
            ])
            ->add('taxCalculationStrategy', ShippingTaxCalculationStrategyChoiceType::class, [
                'label' => 'coreshop_shipping_tax_calc_strategy',
                'priority' => 60,
            ])
            ->add('hideFromCheckout', CheckboxType::class, [
                'label' => 'coreshop_carrier_hideFromCheckout',
                'priority' => 50,
            ])
            ->add('shippingRules', ShippingRuleGroupCollectionType::class)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_carrier';
    }
}

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

namespace CoreShop\Bundle\CoreBundle\Form\Type\Demo;

use CoreShop\Bundle\AddressBundle\Form\Type\CountryChoiceType;
use CoreShop\Bundle\AddressBundle\Form\Type\StateChoiceType;
use CoreShop\Bundle\AddressBundle\Form\Type\ZoneChoiceType;
use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentProviderChoiceType;
use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use CoreShop\Bundle\TaxationBundle\Form\Type\TaxRateChoiceType;
use CoreShop\Bundle\TaxationBundle\Form\Type\TaxRuleGroupChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class EntityChoiceDemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country', CountryChoiceType::class, [
                'label' => 'Country (Single)',
                'required' => false,
            ])
            ->add('countries', CountryChoiceType::class, [
                'label' => 'Countries (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('state', StateChoiceType::class, [
                'label' => 'State (Single)',
                'required' => false,
            ])
            ->add('states', StateChoiceType::class, [
                'label' => 'States (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('zone', ZoneChoiceType::class, [
                'label' => 'Zone',
                'required' => false,
            ])
            ->add('zones', ZoneChoiceType::class, [
                'label' => 'Zones (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'label' => 'Currency (Single)',
                'required' => false,
            ])
            ->add('currencies', CurrencyChoiceType::class, [
                'label' => 'Currencies (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('store', StoreChoiceType::class, [
                'label' => 'Store',
                'required' => false,
            ])
            ->add('stores', StoreChoiceType::class, [
                'label' => 'Stores (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('paymentProvider', PaymentProviderChoiceType::class, [
                'label' => 'Payment Provider (Single)',
                'required' => false,
            ])
            ->add('paymentProviders', PaymentProviderChoiceType::class, [
                'label' => 'Payment Providers (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('taxRate', TaxRateChoiceType::class, [
                'label' => 'Tax Rate (Single)',
                'required' => false,
            ])
            ->add('taxRates', TaxRateChoiceType::class, [
                'label' => 'Tax Rates (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
            ->add('taxRuleGroup', TaxRuleGroupChoiceType::class, [
                'label' => 'Tax Rule Group (Single)',
                'required' => false,
            ])
            ->add('taxRuleGroups', TaxRuleGroupChoiceType::class, [
                'label' => 'Tax Rule Groups (Multiple)',
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_demo_entity_choices';
    }
}

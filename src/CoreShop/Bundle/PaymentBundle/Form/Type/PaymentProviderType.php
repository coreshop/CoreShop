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

namespace CoreShop\Bundle\PaymentBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\PimcoreAssetChoiceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class PaymentProviderType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class, [
                'label' => 'coreshop_identifier',
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => PaymentProviderTranslationType::class,
            ])
            ->add('position', IntegerType::class, [
                'label' => 'coreshop_position',
                'required' => false,
            ])
            ->add('logo', PimcoreAssetChoiceType::class, [
                'label' => 'coreshop_logo',
                'required' => false,
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'coreshop_active',
                'required' => false,
            ])
            ->add('paymentProviderRules', PaymentProviderRuleGroupCollectionType::class)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_payment_provider';
    }
}

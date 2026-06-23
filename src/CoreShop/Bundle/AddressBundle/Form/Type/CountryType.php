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

namespace CoreShop\Bundle\AddressBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use CoreShop\Bundle\ResourceBundle\Form\Type\TagCollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CountryType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => CountryTranslationType::class,
            ])
            ->add('isoCode', TextType::class, [
                'label' => 'coreshop_country_isoCode',
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'coreshop_active',
            ])
            ->add('zone', ZoneChoiceType::class, [
                'label' => 'coreshop_zone',
                'active' => null,
            ])
            ->add('addressFormat', TextareaType::class, [
                'label' => 'coreshop_country_addressFormat',
            ])
            ->add('salutations', TagCollectionType::class, [
                'label' => 'coreshop_country_salutations',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_country';
    }
}

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

namespace CoreShop\Bundle\IndexBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FilterConditionSearchType extends AbstractType
{
    /**
     * @param string[] $validationGroups
     */
    public function __construct(
        protected array $validationGroups,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'coreshop_filters_search_condition_name'])
            ->add('fields', CollectionType::class, [
                'label' => 'coreshop_filters_fields',
                'allow_add' => true,
                'allow_delete' => true,
                'entry_type' => TextType::class,
            ])
            ->add('searchTerm', TextType::class, ['label' => 'coreshop_filters_search_term'])
            ->add('concatenator', ChoiceType::class, [
                'label' => 'coreshop_filters_search_patterns_concatenator',
                'choices' => [
                    'OR' => 'OR',
                    'AND' => 'AND',
                ],
            ])
            ->add('pattern', ChoiceType::class, [
                'label' => 'coreshop_filters_search_patterns_label',
                'choices' => [
                    'Contains' => 'both',
                    'Begins with' => 'left',
                    'Ends with' => 'right',
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_filter_condition_type_search';
    }
}

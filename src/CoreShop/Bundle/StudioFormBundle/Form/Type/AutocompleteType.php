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

namespace CoreShop\Bundle\StudioFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A form type for server-side autocomplete fields.
 *
 * Instead of loading all choices into memory (like ChoiceType),
 * this type only provides metadata (search URL, class name).
 * The frontend renders a search-based select that fetches results on demand.
 */
class AutocompleteType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['autocomplete_url'] = $options['autocomplete_url'];
        $view->vars['autocomplete_class'] = $options['autocomplete_class'] ?? null;
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['min_chars'] = $options['min_chars'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'autocomplete_url' => null,
            'autocomplete_class' => null,
            'multiple' => false,
            'min_chars' => 1,
            'compound' => false,
        ]);

        $resolver->setAllowedTypes('autocomplete_url', ['string', 'null']);
        $resolver->setAllowedTypes('autocomplete_class', ['string', 'null']);
        $resolver->setAllowedTypes('multiple', 'bool');
        $resolver->setAllowedTypes('min_chars', 'int');
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_autocomplete';
    }
}

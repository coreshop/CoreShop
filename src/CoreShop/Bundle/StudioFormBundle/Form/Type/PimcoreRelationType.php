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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A form type for Pimcore Data Object relation fields (ManyToOne / ManyToMany).
 *
 * Provides metadata (class name) to the frontend which renders
 * Pimcore's native relation picker components.
 */
class PimcoreRelationType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['relation_class'] = $options['relation_class'] ?? null;
        $view->vars['multiple'] = $options['multiple'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'relation_class' => null,
            'multiple' => false,
            'compound' => false,
        ]);

        $resolver->setAllowedTypes('relation_class', ['string', 'null']);
        $resolver->setAllowedTypes('multiple', 'bool');
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_pimcore_relation';
    }
}

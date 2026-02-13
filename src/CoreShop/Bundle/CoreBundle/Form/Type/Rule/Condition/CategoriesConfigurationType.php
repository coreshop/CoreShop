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

namespace CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition;

use CoreShop\Bundle\StudioFormBundle\Form\Type\AutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class CategoriesConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', AutocompleteType::class, [
                'autocomplete_class' => 'CoreShopCategory',
                'multiple' => true,
            ])
            ->add('recursive', CheckboxType::class)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_rule_condition_categories';
    }
}

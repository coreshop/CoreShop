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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerCompanyAssignmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newCompanyName', TextType::class, [
                'label' => 'coreshop_customer_transformer_assignment_form_company_name',
                'required' => false,
            ])
            ->add('addressAssignmentType', ChoiceType::class, [
                'label' => 'coreshop_customer_transformer_assignment_form_address_assignment_type',
                'choices' => [
                    'coreshop_customer_transformer_assignment_form_assignment_type_keep' => 'keep',
                    'coreshop_customer_transformer_assignment_form_assignment_type_move' => 'move',
                ],
                'required' => true,
            ])
            ->add('addressAccessType', ChoiceType::class, [
                'label' => 'coreshop_customer_transformer_assignment_form_assignment_address_access_type',
                'choices' => [
                    'coreshop_customer_transformer_assignment_form_assignment_address_access_own_only' => 'own_only',
                    'coreshop_customer_transformer_assignment_form_assignment_address_access_company_only' => 'company_only',
                    'coreshop_customer_transformer_assignment_form_assignment_address_access_own_and_company' => 'own_and_company',
                ],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'studio',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_customer_company_assignment';
    }
}

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

namespace CoreShop\Bundle\ShippingBundle\Form\Type\Rule\Condition;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class DimensionConfigurationType extends AbstractType
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
            ->add('height', IntegerType::class, [
                'label' => 'coreshop_condition_dimension_height',
                'constraints' => [
                    new NotBlank(groups: $this->validationGroups),
                    new Type(type: 'numeric', groups: $this->validationGroups),
                ],
            ])
            ->add('width', IntegerType::class, [
                'label' => 'coreshop_condition_dimension_width',
                'constraints' => [
                    new NotBlank(groups: $this->validationGroups),
                    new Type(type: 'numeric', groups: $this->validationGroups),
                ],
            ])
            ->add('depth', IntegerType::class, [
                'label' => 'coreshop_condition_dimension_depth',
                'constraints' => [
                    new NotBlank(groups: $this->validationGroups),
                    new Type(type: 'numeric', groups: $this->validationGroups),
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_shipping_rule_condition_dimension';
    }
}

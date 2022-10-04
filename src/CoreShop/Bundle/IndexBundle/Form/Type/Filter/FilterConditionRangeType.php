<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;

final class FilterConditionRangeType extends AbstractType
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
            ->add('field', TextType::class)
            ->add('preSelectMin', NumberType::class, [
                'constraints' => [
                    new Type(['type' => 'numeric', 'groups' => $this->validationGroups]),
                ],
            ])
            ->add('preSelectMax', NumberType::class, [
                'constraints' => [
                    new Type(['type' => 'numeric', 'groups' => $this->validationGroups]),
                ],
            ])
            ->add('stepCount', NumberType::class, [
                'constraints' => [
                    new Type(['type' => 'numeric', 'groups' => $this->validationGroups]),
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_filter_condition_type_range';
    }
}

<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\IndexBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;

final class FilterConditionRangeType extends AbstractType
{
    /**
     * @var string[]
     */
    protected array $validationGroups = [];

    /**
     * @param string[] $validationGroups
     */
    public function __construct(array $validationGroups)
    {
        $this->validationGroups = $validationGroups;
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
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_filter_condition_type_range';
    }
}

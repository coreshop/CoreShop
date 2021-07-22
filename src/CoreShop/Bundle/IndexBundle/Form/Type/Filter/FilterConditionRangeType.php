<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
     * @var string[]
     */
    protected $validationGroups = [];

    /**
     * @param string[] $validationGroups
     */
    public function __construct(array $validationGroups)
    {
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_filter_condition_type_range';
    }
}

<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Form\Type;

use CoreShop\Bundle\RuleBundle\Form\Type\RuleType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ProductQuantityPriceRuleType extends RuleType
{
    /**
     * @var array
     */
    protected $calculatorTypes;

    /**
     * @param string $dataClass
     * @param array  $validationGroups
     * @param array  $calculatorTypes
     */
    public function __construct($dataClass, array $validationGroups, array $calculatorTypes)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->calculatorTypes = $calculatorTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextareaType::class)
            ->add('calculationBehaviour', ChoiceType::class, [
                'choices' => $this->calculatorTypes,
                'constraints' => [
                    new NotBlank(['groups' => 'coreshop'])
                ]
            ])
            ->add('active', CheckboxType::class)
            ->add('priority', NumberType::class)
            ->add('conditions', ProductQuantityPriceRuleConditionCollectionType::class)
            ->add('ranges', ProductQuantityRangeCollectionType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_quantity_price_rule';
    }
}

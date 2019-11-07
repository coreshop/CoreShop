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

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductQuantityRangeType extends AbstractResourceType
{
    /**
     * @var array
     */
    protected $actionTypes;

    /**
     * @var array
     */
    protected $actionConstraints;

    /**
     * @param string $dataClass
     * @param array  $validationGroups
     * @param array  $actionTypes
     * @param array  $actionConstraints
     */
    public function __construct($dataClass, array $validationGroups, array $actionTypes, array $actionConstraints)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->actionTypes = $actionTypes;
        $this->actionConstraints = $actionConstraints;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('rangeStartingFrom', NumberType::class)
            ->add('pricingBehaviour', ChoiceType::class, [
                'choices' => $this->actionTypes,
            ])
            ->add('highlighted', CheckboxType::class, []);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $constraints = [];
        foreach ($this->actionConstraints as $constraint) {
            $constraintClass = $constraint['class'];
            $constraints[] = new $constraintClass(['groups' => $constraint['groups']]);
        }

        $resolver->setDefaults([
            'constraints' => $constraints,
            'validation_groups' => function (FormInterface $form) {
                $validationGroups = ['coreshop_product_quantity_price_rules_range_validation_default'];
                /** @var QuantityRangeInterface $data */
                $data = $form->getData();
                $validationGroups[] = sprintf('coreshop_product_quantity_price_rules_range_validation_behaviour_%s', $data->getPricingBehaviour());

                return $validationGroups;
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_quantity_price_rules_range';
    }
}

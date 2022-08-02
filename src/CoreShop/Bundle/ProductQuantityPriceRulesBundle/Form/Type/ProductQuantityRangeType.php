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

declare(strict_types=1);

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
    public function __construct(string $dataClass, array $validationGroups, protected array $actionTypes, protected array $actionConstraints)
    {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('rangeStartingFrom', NumberType::class)
            ->add('pricingBehaviour', ChoiceType::class, [
                'choices' => $this->actionTypes,
            ])
            ->add('highlighted', CheckboxType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver): void
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

    public function getBlockPrefix(): string
    {
        return 'coreshop_product_quantity_price_rules_range';
    }
}

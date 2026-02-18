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

namespace CoreShop\Bundle\OrderBundle\Form\Type\Rule\Condition;

use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class NotCombinableConfigurationType extends AbstractType
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
            ->add('price_rules', CartPriceRuleChoiceType::class, [
                'label' => 'coreshop_cart_pricerules',
                'multiple' => true,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_cart_price_rule_condition_not_combinable';
    }
}

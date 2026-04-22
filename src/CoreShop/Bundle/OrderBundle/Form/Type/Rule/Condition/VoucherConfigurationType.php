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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

final class VoucherConfigurationType extends AbstractType
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
            ->add('maxUsagePerCode', NumberType::class, [
                'label' => 'coreshop_action_voucher_max_usage_per_code',
            ])
            ->add('maxUsagePerUser', NumberType::class, [
                'label' => 'coreshop_action_voucher_max_usage_per_user',
            ])
            ->add('onlyOnePerCart', CheckboxType::class, [
                'label' => 'coreshop_action_voucher_only_one_per_cart',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_cart_price_rule_condition_voucher';
    }
}

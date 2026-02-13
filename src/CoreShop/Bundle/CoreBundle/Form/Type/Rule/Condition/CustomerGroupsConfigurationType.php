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

namespace CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition;

use CoreShop\Bundle\CoreBundle\Form\Type\CustomerGroupChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerGroupsConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerGroups', CustomerGroupChoiceType::class, [
                'multiple' => true,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_rule_condition_customer_groups';
    }
}

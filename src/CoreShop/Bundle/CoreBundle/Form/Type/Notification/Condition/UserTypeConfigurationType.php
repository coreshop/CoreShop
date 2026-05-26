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

namespace CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition;

use CoreShop\Component\Core\Notification\Rule\Condition\User\UserTypeChecker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class UserTypeConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userType', ChoiceType::class, [
                'label' => 'coreshop_condition_userType',
                'choices' => [
                    'coreshop_condition_userType_password_reset' => UserTypeChecker::TYPE_PASSWORD_RESET,
                    'coreshop_condition_userType_register' => UserTypeChecker::TYPE_REGISTER,
                    'coreshop_condition_userType_newsletter_double_opt_in' => UserTypeChecker::TYPE_NEWSLETTER_DOUBLE_OPT_IN,
                    'coreshop_condition_userType_newsletter_confirmed' => UserTypeChecker::TYPE_NEWSLETTER_CONFIRMED,
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_notification_condition_user_type';
    }
}

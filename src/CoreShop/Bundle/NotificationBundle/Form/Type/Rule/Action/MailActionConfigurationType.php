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

namespace CoreShop\Bundle\NotificationBundle\Form\Type\Rule\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class MailActionConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mails', CollectionType::class, [
                'label' => 'coreshop_email_document',
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('doNotSendToDesignatedRecipient', CheckboxType::class, [
                'label' => 'coreshop_mail_rule_do_not_send_to_designated_recipient',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_notification_rule_action_mail';
    }
}

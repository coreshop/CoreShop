<?php

namespace CoreShop\Bundle\CoreBundle\Form\Type\Notification\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class OrderMailConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mails', CollectionType::class, [
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('sendInvoices', CheckboxType::class)
            ->add('sendShipments', CheckboxType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_notification_rule_action_order_mail';
    }
}
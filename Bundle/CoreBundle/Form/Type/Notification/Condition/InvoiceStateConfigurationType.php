<?php

namespace CoreShop\Bundle\CoreBundle\Form\Type\Notification\Condition;

use CoreShop\Component\Core\Notification\Rule\Condition\Order\InvoiceStateChecker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class InvoiceStateConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invoiceState', ChoiceType::class, [
                'choices' => [
                    InvoiceStateChecker::INVOICE_TYPE_PARTIAL,
                    InvoiceStateChecker::INVOICE_TYPE_FULL,
                    InvoiceStateChecker::INVOICE_TYPE_ALL
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_notification_condition_invoice_state';
    }
}

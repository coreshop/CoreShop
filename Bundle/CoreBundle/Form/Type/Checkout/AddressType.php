<?php

namespace CoreShop\Bundle\CoreBundle\Form\Type\Checkout;

use CoreShop\Bundle\CoreBundle\Form\Type\AddressChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

final class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingAddress', AddressChoiceType::class, [
                'constraints' => [new Valid()],
                'customer' => $options['customer'],
                'label' => 'coreshop.checkout.address.shipping',
            ])
            ->add('invoiceAddress', AddressChoiceType::class, [
                'constraints' => [new Valid()],
                'customer' => $options['customer'],
                'label' => 'coreshop.checkout.address.invoice',
            ])
            ->add('useShippingAsInvoice', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'data' => true,
                'label' => 'coreshop.checkout.address.use_shipping_as_invoice',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('customer');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_checkout_address';
    }
}

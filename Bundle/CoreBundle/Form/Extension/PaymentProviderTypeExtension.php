<?php

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentProviderType;
use CoreShop\Bundle\PayumBundle\Form\Type\GatewayConfigType;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PaymentProviderTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stores', StoreChoiceType::class, [
                'multiple' => true,
                'expanded' => true
            ])
            ->add('gatewayConfig', GatewayConfigType::class, [
                'label' => false
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $paymentMethod = $event->getData();

                if (!$paymentMethod instanceof PaymentProviderInterface) {
                    return;
                }

                $gatewayConfig = $paymentMethod->getGatewayConfig();
                if (null === $gatewayConfig->getGatewayName()) {
                    $gatewayConfig->setGatewayName($paymentMethod->getIdentifier());
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return PaymentProviderType::class;
    }
}

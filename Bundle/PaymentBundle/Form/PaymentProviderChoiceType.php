<?php

namespace CoreShop\Bundle\PaymentBundle\Form\Type;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PaymentProviderChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $paymentProviderRepository;

    /**
     * @param RepositoryInterface $paymentProviderRepository
     */
    public function __construct(RepositoryInterface $paymentProviderRepository)
    {
        $this->paymentProviderRepository = $paymentProviderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    $paymentProvider = $this->paymentProviderRepository->findAll();

                    /*
                     * PHP 5.* bug, fixed in PHP 7: https://bugs.php.net/bug.php?id=50688
                     * "usort(): Array was modified by the user comparison function"
                     */
                    @usort($paymentProvider, function ($a, $b) {
                        return $a->getName() < $b->getName() ? -1 : 1;
                    });

                    return $paymentProvider;
                },
                'choice_value' => 'id',
                'choice_label' => function($paymentProvider) {
                    return $paymentProvider->getName();
                },
                'choice_translation_domain' => false,
                'active' => true
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_payment_provider_choice';
    }
}

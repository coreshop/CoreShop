<?php

namespace CoreShop\Bundle\CoreBundle\Form\Type;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddressChoiceType extends AbstractType
{
    /**
     * @var PimcoreRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param PimcoreRepositoryInterface $customerRepository
     */
    public function __construct(PimcoreRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('customer');
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    $customer = $this->customerRepository->find($options['customer']);

                    if (!$customer instanceof CustomerInterface) {
                        throw new \InvalidArgumentException('Customer needs to be set');
                    }

                    return $customer->getAddresses();
                },
                'choice_value' => 'o_id',
                'choice_label' => function ($value, $key, $index) {
                    if ($value instanceof AddressInterface) {
                        return sprintf('%s %s', $value->getStreet(), $value->getNumber());
                    }

                    return null;
                },
                'choice_translation_domain' => false,
                'active' => true,
                'label' => 'coreshop.form.address.country',
                'placeholder' => 'coreshop.form.country.select',
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
        return 'coreshop_customer_address_choice';
    }
}

<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Form\Type;

use CoreShop\Bundle\AddressBundle\Form\Type\AddressType;
use CoreShop\Bundle\CoreBundle\Form\EventSubscriber\CustomerRegistrationFormSubscriber;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Valid;

class CustomerRegistrationType extends AbstractResourceType
{
    /**
     * @var DataMapperInterface
     */
    private $dataMapper;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param string                      $dataClass
     * @param array                       $validationGroups
     * @param DataMapperInterface         $dataMapper
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        $dataClass,
        array $validationGroups,
        DataMapperInterface $dataMapper,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->dataMapper = $dataMapper;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new CustomerRegistrationFormSubscriber($this->customerRepository));

        $builder->setDataMapper($this->dataMapper);
        $builder
            ->add('user', UserRegistrationType::class, [
                'label' => false,
                'constraints' => [new Valid(['groups' => ['coreshop']])],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'coreshop.form.customer.gender',
                'choices' => array(
                    'coreshop.form.customer.gender.male' => 'male',
                    'coreshop.form.customer.gender.female' => 'female',
                ),
            ])
            ->add('firstname', TextType::class, [
                'label' => 'coreshop.form.customer.firstname',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'coreshop.form.customer.lastname',
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => 'coreshop.form.customer.email.must_match',
                'first_options' => ['label' => 'coreshop.form.customer.email'],
                'second_options' => ['label' => 'coreshop.form.customer.email_repeat'],
            ])
            ->add('newsletterActive', CheckboxType::class, [
                'label' => 'coreshop.form.customer.newsletter.subscribe',
                'required' => false,
            ])
            ->add('address', AddressType::class, [
                'label' => 'coreshop.form.customer_registration.address',
                'label_attr' => [
                    'class' => 'cs-address',
                ],
                'constraints' => [
                    new Valid(['groups' => ['coreshop']]),
                ],
                'mapped' => false,
            ])
            ->add('termsAccepted', CheckboxType::class, [
                'label' => 'coreshop.form.customer.terms',
                'mapped' => false,
                'validation_groups' => ['coreshop'],
                'constraints' => new IsTrue(['groups' => ['coreshop']]),
            ])
            ->add('submit', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_customer_registration';
    }
}

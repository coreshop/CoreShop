<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Form\Type;

use CoreShop\Bundle\CoreBundle\Form\Type\Checkout\AddressType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Customer\Repository\CustomerRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;

class GuestRegistrationType extends AbstractResourceType
{
    private DataMapperInterface $dataMapper;
    private CustomerRepositoryInterface $customerRepository;
    private FactoryInterface $customerFactory;

    public function __construct(
        string $dataClass,
        array $validationGroups,
        DataMapperInterface $dataMapper,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->dataMapper = $dataMapper;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setDataMapper($this->dataMapper);

        $builder
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
            ->add('address', AddressType::class, [
                'label' => 'coreshop.form.customer_registration.address',
                'label_attr' => [
                    'class' => 'cs-address',
                ],
                'constraints' => [
                    new Valid(['groups' => $this->validationGroups]),
                ],
            ])
            ->add('termsAccepted', CheckboxType::class, [
                'label' => 'coreshop.form.customer.terms',
                'mapped' => false,
                'validation_groups' => ['coreshop'],
            ])
            ->add('submit', SubmitType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
                $data = $event->getData();
                $form = $event->getForm();
                $formCustomer = $form->getData();

                if (!isset($data['email'])) {
                    return;
                }

                /**
                 * @var CustomerInterface $customer
                 */
                $customer = $this->customerRepository->findGuestByEmail($data['email']);

                // assign existing customer or create a new one
                $form = $event->getForm();
                if (null !== $customer && null === $customer->getUser()) {
                    $form->setData($customer);

                    return;
                }

                if (null === $formCustomer || null !== $formCustomer->getUser()) {
                    $customer = $this->customerFactory->createNew();
                    $customer->setEmail($data['email']);
                    $form->setData($customer);
                }
            })
            ->setDataLocked(false);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_guest_registration';
    }
}

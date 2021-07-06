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

namespace CoreShop\Bundle\CustomerBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractResourceType
{
    protected $guestValidationGroups = [];

    public function __construct(string $dataClass, array $validationGroups = [], array $guestValidationGroups = [])
    {
        parent::__construct($dataClass, $validationGroups);

        $this->guestValidationGroups = $guestValidationGroups;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
            ]);

        if ($options['use_repeat_email']) {
            $builder
                ->add('email', RepeatedType::class, [
                    'type' => EmailType::class,
                    'invalid_message' => 'coreshop.form.customer.email.must_match',
                    'first_options' => ['label' => 'coreshop.form.customer.email'],
                    'second_options' => ['label' => 'coreshop.form.customer.email_repeat'],

                ]);
        } else {
            $builder->add('email', EmailType::class);
        }

        if ($options['allow_username']) {
            $builder->add('username', TextType::class, [
                'label' => 'coreshop.form.customer.username',
            ]);
        }

        if (!$options['guest'] && $options['allow_password_field']) {
            $builder
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'coreshop.form.customer.password.must_match',
                    'first_options' => ['label' => 'coreshop.form.customer.password'],
                    'second_options' => ['label' => 'coreshop.form.customer.password_repeat'],
                ]);
        }

        if (!$options['guest']) {
            $builder
                ->add('newsletterActive', CheckboxType::class, [
                    'label' => 'coreshop.form.customer.newsletter.subscribe',
                    'required' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('use_repeat_email', true);
        $resolver->setDefault('guest', false);
        $resolver->setDefault('allow_password_field', false);
        $resolver->setDefault('allow_username', false);
        $resolver->setDefault('customer', false);
        $resolver->setDefault('csrf_protection', true);
        $resolver->setDefaults(array(
            'validation_groups' => function (FormInterface $form) {
                $isGuest = $form->getConfig()->getOption('guest');
                $validationGroups = $this->validationGroups;

                if ($isGuest) {
                    return $this->guestValidationGroups;
                }

                return $validationGroups;
            },
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_customer';
    }
}

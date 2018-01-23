<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CustomerBundle\Form\Type;

use CoreShop\Bundle\CoreBundle\Form\Type\AddressChoiceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'coreshop.form.customer.firstname'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'coreshop.form.customer.lastname'
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'first_options' => ['label' => 'coreshop.form.customer.email'],
                'second_options' => ['label' => 'coreshop.form.customer.email_repeat']
            ]);

        if (!$options['guest']) {
            $builder
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'coreshop.form.customer.password'],
                    'second_options' => ['label' => 'coreshop.form.customer.password_repeat']
                ]);
        }

        if ($options['allow_default_address'] && $options['customer']) {
            $builder->add('defaultAddress', AddressChoiceType::class, [
                'customer' => $options['customer'],
                'label' => 'coreshop.form.customer.default_address',
            ]);
        }

        $builder
            ->add('gender', ChoiceType::class, [
                'label' => 'coreshop.form.customer.gender',
                'choices' => array(
                    'coreshop.form.customer.gender.male' => 'male',
                    'coreshop.form.customer.gender.female' => 'female'
                ),
            ]);

        if (!$options['guest']) {
            $builder
                ->add('newsletterActive', ChoiceType::class, [
                    'label' => 'coreshop.form.customer.newsletter',
                    'choices' => array(
                        'coreshop.form.customer.newsletter.subscribe' => true,
                        'coreshop.form.customer.gender.un_subscribe' => false
                    ),
                    'expanded' => true
                ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('guest', false);
        $resolver->setDefault('allow_default_address', false);
        $resolver->setDefault('customer', false);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_customer';
    }
}

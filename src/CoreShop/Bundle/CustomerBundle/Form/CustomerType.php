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

namespace CoreShop\Bundle\CustomerBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractResourceType
{
    /**
     * @var string[]
     */
    protected $guestValidationGroups = [];

    /**
     * @param string   $dataClass             FQCN
     * @param string[] $validationGroups
     * @param string[] $guestValidationGroups
     */
    public function __construct($dataClass, array $validationGroups = [], array $guestValidationGroups = [])
    {
        parent::__construct($dataClass, $validationGroups);

        $this->guestValidationGroups = $guestValidationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => 'coreshop.form.customer.email.must_match',
                'first_options' => ['label' => 'coreshop.form.customer.email'],
                'second_options' => ['label' => 'coreshop.form.customer.email_repeat'],
            ]);

        $builder
            ->add('newsletterActive', CheckboxType::class, [
                'label' => 'coreshop.form.customer.newsletter.subscribe',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

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

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

namespace CoreShop\Bundle\AddressBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class AddressType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', TextType::class, [
                'label' => 'coreshop.form.address.company',
                'required' => false,
            ])
            ->add('salutation', SalutationChoiceType::class, [
                'label' => 'coreshop.form.address.salutation',
            ])
            ->add('firstname', TextType::class, [
                'label' => 'coreshop.form.address.firstname',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'coreshop.form.address.lastname',
            ])
            ->add('street', TextType::class, [
                'label' => 'coreshop.form.address.street',
            ])
            ->add('number', TextType::class, [
                'label' => 'coreshop.form.address.number',
            ])
            ->add('postcode', TextType::class, [
                'label' => 'coreshop.form.address.postcode',
            ])
            ->add('city', TextType::class, [
                'label' => 'coreshop.form.address.city',
            ])
            ->add('country', CountryChoiceType::class, [
                'active' => true,
                'label' => 'coreshop.form.address.country',
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'coreshop.form.address.phone_number',
                'required' => false,
            ])
            ->add('addressType', HiddenType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('_redirect', HiddenType::class, array(
                'mapped' => false,
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_address';
    }
}

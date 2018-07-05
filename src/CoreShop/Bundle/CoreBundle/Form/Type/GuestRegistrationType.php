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

namespace CoreShop\Bundle\CoreBundle\Form\Type;

use CoreShop\Bundle\AddressBundle\Form\Type\AddressType;
use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

class GuestRegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', CustomerType::class, [
                'label' => 'coreshop.form.customer_registration.customer',
                'label_attr' => [
                    'class' => 'cs-customer',
                ],
                'guest' => true,
                'constraints' => [
                    new Valid(['groups' => ['coreshop_customer_guest']]),
                ],
            ])
            ->add('address', AddressType::class, [
                'label' => 'coreshop.form.customer_registration.address',
                'label_attr' => [
                    'class' => 'cs-address',
                ],
                'constraints' => [
                    new Valid(['groups' => ['coreshop']]),
                ],
            ])

            ->add('submit', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_guest_registration';
    }
}

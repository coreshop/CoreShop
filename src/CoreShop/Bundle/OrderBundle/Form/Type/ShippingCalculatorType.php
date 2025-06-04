<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\AddressBundle\Form\Type\CountryChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ShippingCalculatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country', CountryChoiceType::class, [
                'active' => true,
                'label' => 'coreshop.form.cart.carrier.country',
            ])
            ->add('zip', TextType::class, [
                'label' => 'coreshop.form.cart.carrier.zip',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'coreshop.form.cart.carrier.submit',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_shipping_calculator';
    }
}

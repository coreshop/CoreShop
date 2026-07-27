<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\CurrencyBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CurrencyType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'coreshop_name',
            ])
            ->add('isoCode', TextType::class, [
                'label' => 'coreshop_currency_isoCode',
            ])
            ->add('numericIsoCode', IntegerType::class, [
                'label' => 'coreshop_currency_numericIsoCode',
            ])
            ->add('symbol', TextType::class, [
                'label' => 'coreshop_currency_symbol',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_currency';
    }
}

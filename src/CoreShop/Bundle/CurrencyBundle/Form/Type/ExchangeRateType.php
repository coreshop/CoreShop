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

namespace CoreShop\Bundle\CurrencyBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ExchangeRateType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('exchangeRate', NumberType::class, [
                'required' => true,
                'scale' => 10,
                'rounding_mode' => $options['rounding_mode'],
            ])
            ->add('fromCurrency', CurrencyChoiceType::class, [
                'required' => true,
                'empty_data' => false,
            ])
            ->add('toCurrency', CurrencyChoiceType::class, [
                'required' => true,
                'empty_data' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('rounding_mode', \NumberFormatter::ROUND_HALFEVEN);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_exchange_rate';
    }
}

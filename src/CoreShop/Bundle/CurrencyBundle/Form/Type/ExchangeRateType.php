<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
            ]);
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

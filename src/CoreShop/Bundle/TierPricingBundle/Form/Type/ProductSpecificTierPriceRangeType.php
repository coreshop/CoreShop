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

namespace CoreShop\Bundle\TierPricingBundle\Form\Type;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use CoreShop\Bundle\MoneyBundle\Form\Type\MoneyType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductSpecificTierPriceRangeType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('rangeFrom', IntegerType::class, [])
            ->add('rangeTo', IntegerType::class, [])
            ->add('pricingBehaviour', ChoiceType::class, [
                'choices' => [
                    'fixed'               => 'fixed',
                    'amount_discount'     => 'amount_discount',
                    'amount_increase'     => 'amount_increase',
                    'percentage_discount' => 'percentage_discount',
                    'percentage_increase' => 'percentage_increase',
                ]
            ])
            ->add('amount', MoneyType::class, [])
            ->add('currency', CurrencyChoiceType::class, [])
            ->add('percentage', NumberType::class, [])
            ->add('pseudoPrice', MoneyType::class, [])
            ->add('highlighted', CheckboxType::class, []);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_product_specific_tier_price_range';
    }
}

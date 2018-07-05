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

namespace CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use CoreShop\Bundle\MoneyBundle\Form\Type\MoneyType;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class DiscountAmountConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', MoneyType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']]),
                    new Type(['type' => 'numeric', 'groups' => ['coreshop']]),
                ],
            ])
            ->add('gross', CheckboxType::class, [
            ])
            ->add('applyOn', ChoiceType::class, [
                'choices' => [
                    'total' => 'total',
                    'subtotal' => 'subtotal',
                ],
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'constraints' => [
                    new NotBlank(['groups' => ['coreshop']]),
                ],
            ]);

        $builder->get('currency')->addModelTransformer(new CallbackTransformer(
            function ($currency) {
                if ($currency instanceof CurrencyInterface) {
                    return $currency->getId();
                }

                return null;
            },
            function ($currency) {
                if ($currency instanceof CurrencyInterface) {
                    return $currency->getId();
                }

                return null;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_cart_price_rule_action_discount_amount';
    }
}

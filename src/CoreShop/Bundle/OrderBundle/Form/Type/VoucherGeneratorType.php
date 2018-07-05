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

namespace CoreShop\Bundle\OrderBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Order\Generator\CartPriceRuleVoucherCodeGenerator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class VoucherGeneratorType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', IntegerType::class)
            ->add('length', IntegerType::class)
            ->add('format', ChoiceType::class, [
                'choices' => [
                    CartPriceRuleVoucherCodeGenerator::FORMAT_ALPHABETIC,
                    CartPriceRuleVoucherCodeGenerator::FORMAT_ALPHANUMERIC,
                    CartPriceRuleVoucherCodeGenerator::FORMAT_NUMERIC,
                ],
            ])
            ->add('prefix', TextType::class)
            ->add('suffix', TextType::class)
            ->add('hyphensOn', IntegerType::class)
            ->add('cartPriceRule', CartPriceRuleChoiceType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_voucher_generator';
    }
}

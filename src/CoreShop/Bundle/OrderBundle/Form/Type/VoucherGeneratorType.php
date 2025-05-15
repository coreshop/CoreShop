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

use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Order\Generator\CartPriceRuleVoucherCodeGenerator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class VoucherGeneratorType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ->add('cartPriceRule', CartPriceRuleChoiceType::class)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_voucher_generator';
    }
}

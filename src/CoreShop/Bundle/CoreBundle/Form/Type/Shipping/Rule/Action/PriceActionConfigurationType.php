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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Form\Type\Shipping\Rule\Action;

use CoreShop\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use CoreShop\Bundle\MoneyBundle\Form\Type\MoneyType;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PriceActionConfigurationType extends AbstractType
{
    public function __construct(protected array $validationGroups)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', MoneyType::class, [
                'constraints' => [
                    new NotBlank(['groups' => $this->validationGroups]),
                ],
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'constraints' => [
                    new NotBlank(['groups' => $this->validationGroups]),
                ],
            ])
        ;

        $builder->get('currency')->addModelTransformer(new CallbackTransformer(
            function (mixed $currency): mixed {
                if ($currency instanceof CurrencyInterface) {
                    return $currency->getId();
                }

                return null;
            },
            function (mixed $currency): mixed {
                if ($currency instanceof CurrencyInterface) {
                    return $currency->getId();
                }

                return null;
            },
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_shipping_rule_action_price';
    }
}

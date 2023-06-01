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

namespace CoreShop\Bundle\PaymentBundle\Form\Type;

use CoreShop\Component\Payment\Model\PaymentProviderRuleInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PaymentProviderRuleChoiceType extends AbstractType
{
    public function __construct(
        private RepositoryInterface $paymentProviderRuleRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    /**
                     * @var PaymentProviderRuleInterface[] $paymentProviderRules
                     */
                    $paymentProviderRules = $this->paymentProviderRuleRepository->findAll();

                    usort($paymentProviderRules, function (PaymentProviderRuleInterface $a, PaymentProviderRuleInterface $b): int {
                        return $a->getName() <=> $b->getName();
                    });

                    return $paymentProviderRules;
                },
                'choice_value' => 'id',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'active' => true,
            ])
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_payment_provider_rule_choice';
    }
}

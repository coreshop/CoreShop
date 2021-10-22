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

namespace CoreShop\Bundle\PaymentBundle\Form\Type;

use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Resolver\PaymentProviderResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PaymentProviderChoiceType extends AbstractType
{
    public function __construct(private PaymentProviderResolverInterface $paymentProviderResolver)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options) {
                    return $this->paymentProviderResolver->resolvePaymentProviders($options['subject']);
                },
                'choice_value' => 'id',
                'choice_label' => function (PaymentProviderInterface $paymentProvider): string {
                    return $paymentProvider->getTitle();
                },
                'choice_translation_domain' => false,
                'active' => true,
                'subject' => null,
            ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $description = [];
        $instructions = [];
        $paymentProvider = $form->getConfig()->getOption('choices');
        foreach ($paymentProvider as $payment) {
            if (!empty($payment->getDescription())) {
                $description[$payment->getId()] = $payment->getDescription();
            }
            if (!empty($payment->getInstructions())) {
                $instructions[$payment->getId()] = $payment->getInstructions();
            }
        }

        $view->vars = array_merge($view->vars, [
            'choices_description' => $description,
            'choices_instruction' => $instructions,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_payment_provider_choice';
    }
}

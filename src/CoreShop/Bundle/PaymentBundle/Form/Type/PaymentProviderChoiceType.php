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

namespace CoreShop\Bundle\PaymentBundle\Form\Type;

use CoreShop\Component\Payment\Resolver\PaymentProviderResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PaymentProviderChoiceType extends AbstractType
{
    /**
     * @var PaymentProviderResolverInterface
     */
    private $paymentProviderResolver;

    /**
     * @param PaymentProviderResolverInterface $paymentProviderResolver
     */
    public function __construct(PaymentProviderResolverInterface $paymentProviderResolver)
    {
        $this->paymentProviderResolver = $paymentProviderResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'choices' => function(Options $options) {
                    $paymentProvider = $this->paymentProviderResolver->resolvePaymentProviders($options['subject']);

                    return $paymentProvider;
                },
                'choice_value' => 'id',
                'choice_label' => function($paymentProvider) {
                    return $paymentProvider->getName();
                },
                'choice_attr' => function($val, $key, $index) {
                    // adds a class like attending_yes, attending_no, etc
                    return ['data-factory' => $val->getGatewayConfig()->getFactoryName()];
                },
                'choice_translation_domain' => false,
                'active' => true,
                'subject' => null
            ]);
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
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
            'choices_instruction' => $instructions
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coreshop_payment_provider_choice';
    }
}

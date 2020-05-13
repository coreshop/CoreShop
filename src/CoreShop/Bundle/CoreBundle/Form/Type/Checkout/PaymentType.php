<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Form\Type\Checkout;

use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentProviderChoiceType;
use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Payment\Repository\PaymentProviderRepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

final class PaymentType extends AbstractResourceType
{
    private $formTypeRegistry;
    private $paymentProviderRepository;
    private $gatewayFactories;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        string $dataClass,
        array $validationGroups,
        FormTypeRegistryInterface $formTypeRegistry,
        PaymentProviderRepositoryInterface $paymentProviderRepository,
        array $gatewayFactories
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->formTypeRegistry = $formTypeRegistry;
        $this->paymentProviderRepository = $paymentProviderRepository;
        $this->gatewayFactories = $gatewayFactories;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('paymentProvider', PaymentProviderChoiceType::class, [
                'constraints' => [new Valid(), new NotBlank(['groups' => $this->validationGroups])],
                'label' => 'coreshop.ui.payment_provider',
                'subject' => $options['payment_subject'],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $type = $this->getRegistryIdentifier($event->getForm(), $event->getData());
                if (null === $type) {
                    return;
                }

                if ($this->formTypeRegistry->has($type, 'default')) {
                    $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($type, 'default'));
                } else {
                    $this->removeConfigurationFields($event->getForm());
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!isset($data['paymentProvider'])) {
                    return;
                }

                $provider = $this->paymentProviderRepository->find($data['paymentProvider']);

                if (!$provider instanceof PaymentProviderInterface) {
                    return;
                }

                $factory = $provider->getGatewayConfig()->getFactoryName();

                if ($this->formTypeRegistry->has($factory, 'default')) {
                    $this->addConfigurationFields($event->getForm(), $this->formTypeRegistry->get($factory, 'default'));
                } else {
                    $this->removeConfigurationFields($event->getForm());
                }
            });

        $prototypes = [];
        foreach (array_keys($this->gatewayFactories) as $type) {
            if (!$this->formTypeRegistry->has($type, 'default')) {
                continue;
            }

            $formBuilder = $builder->create(
                'paymentSettings',
                $this->formTypeRegistry->get($type, 'default')
            );

            $prototypes[$type] = $formBuilder->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['prototypes'] = [];

        foreach ($form->getConfig()->getAttribute('prototypes') as $type => $prototype) {
            /* @var FormInterface $prototype */
            $view->vars['prototypes'][$type] = $prototype->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('payment_subject', null);
    }

    protected function addConfigurationFields(FormInterface $form, string $configurationType): void
    {
        $form->add('paymentSettings', $configurationType, [
            'label' => false,
        ]);
    }

    protected function removeConfigurationFields(FormInterface $form): void
    {
        if (!$form->has('paymentSettings')) {
            return;
        }

        $form->getData()->setPaymentSettings(null);
        $form->remove('paymentSettings');
    }

    protected function getRegistryIdentifier(FormInterface $form, $data = null): ?string
    {
        if ($data instanceof OrderInterface && $data->getPaymentProvider() instanceof PaymentProviderInterface) {
            return $data->getPaymentProvider()->getGatewayConfig()->getFactoryName();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'coreshop_checkout_payment';
    }
}

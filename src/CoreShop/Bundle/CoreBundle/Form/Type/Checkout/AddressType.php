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

namespace CoreShop\Bundle\CoreBundle\Form\Type\Checkout;

use CoreShop\Bundle\CoreBundle\Form\Type\AddressChoiceType;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddressType extends AbstractResourceType
{
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private AddressFormatterInterface $addressFormatHelper,
        private TranslatorInterface $translator,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $defaultShippingAddress = null;
        $defaultInvoiceAddress = null;

        if ($options['customer']->getDefaultAddress() instanceof AddressInterface) {
            /** @var AddressInterface $address */
            $address = $options['customer']->getDefaultAddress();
            $addressIdentifier = $address->getAddressIdentifier();

            if (null === $addressIdentifier) {
                $defaultShippingAddress = $address;
                $defaultInvoiceAddress = $address;
            } else {
                $defaultShippingAddress = $addressIdentifier->getName() === 'shipping' ? $address : null;
                $defaultInvoiceAddress = $addressIdentifier->getName() === 'invoice' ? $address : null;
            }
        }

        $builder
            ->add('shippingAddress', AddressChoiceType::class, [
                'constraints' => [new NotBlank(['groups' => $this->validationGroups])],
                'customer' => $options['customer']->getId(),
                'label' => 'coreshop.form.address.shipping',
                'allowed_address_identifier' => [null, 'shipping'],
                'choice_attr' => function (AddressInterface $address) {
                    return [
                        'data-address' => json_encode(['html' => $this->addressFormatHelper->formatAddress($address)]),
                        'data-address-type' => $address->hasAddressIdentifier() ? $address->getAddressIdentifier()->getName() : '',
                    ];
                },
                'empty_data' => $defaultShippingAddress,
            ])
            ->add('invoiceAddress', AddressChoiceType::class, [
                'constraints' => [new NotBlank(['groups' => $this->validationGroups])],
                'customer' => $options['customer']->getId(),
                'label' => 'coreshop.form.address.invoice',
                'allowed_address_identifier' => [null, 'invoice'],
                'choice_attr' => function (AddressInterface $address) {
                    return [
                        'data-address' => json_encode(['html' => $this->addressFormatHelper->formatAddress($address)]),
                        'data-address-type' => $address->hasAddressIdentifier() ? $address->getAddressIdentifier()->getName() : '',
                    ];
                },
                'empty_data' => $defaultInvoiceAddress,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) {
                /** @var OrderInterface $cart */
                $cart = $event->getData();
                $checkboxData = true;
                $checkboxDisabled = false;

                if ($event->getForm()->has('shippingAddress') &&
                    $event->getForm()->get('shippingAddress')->getConfig()->hasOption('choices')
                ) {
                    $choiceList = $event->getForm()->get('shippingAddress')->getConfig()->getOption('choices');

                    if (!is_array($choiceList) || count($choiceList) === 0) {
                        $checkboxData = null;
                        $checkboxDisabled = true;
                    }
                }

                if ($cart->getShippingAddress() instanceof AddressInterface &&
                    $cart->getInvoiceAddress() instanceof AddressInterface &&
                    $cart->getShippingAddress()->getId() !== $cart->getInvoiceAddress()->getId()
                ) {
                    $checkboxData = null;
                }

                $event->getForm()->add('useInvoiceAsShipping', CheckboxType::class, [
                    'required' => false,
                    'mapped' => false,
                    'disabled' => $checkboxDisabled,
                    'label' => 'coreshop.form.address.use_invoice_as_shipping',
                    'data' => $checkboxData,
                ]);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $formData = $event->getData();

                if (isset($formData['invoiceAddress'], $formData['useInvoiceAsShipping']) && '1' === $formData['useInvoiceAsShipping']) {
                    $valid = true;

                    if ($event->getForm()->has('shippingAddress') &&
                        $event->getForm()->get('shippingAddress')->getConfig()->hasOption('choices')
                    ) {
                        $invoiceAddressId = $formData['invoiceAddress'];
                        $choiceList = $event->getForm()->get('shippingAddress')->getConfig()->getOption('choices');

                        if (is_array($choiceList) && count($choiceList) > 0) {
                            $valid = count(array_filter($choiceList, static function (AddressInterface $address) use ($invoiceAddressId) {
                                return $address->getId() === (int) $invoiceAddressId;
                            })) > 0;
                        }
                    }

                    if ($valid === true) {
                        $formData['shippingAddress'] = $formData['invoiceAddress'];
                        $event->setData($formData);
                    } else {
                        $message = $this->translator->trans('coreshop.checkout.address.invoice_as_shipping_invalid');
                        $event->getForm()->addError(new FormError($message));
                    }
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('customer');
        $resolver->setAllowedTypes('customer', [CustomerInterface::class]);
    }

    public function getBlockPrefix(): string
    {
        return 'coreshop_checkout_address';
    }
}

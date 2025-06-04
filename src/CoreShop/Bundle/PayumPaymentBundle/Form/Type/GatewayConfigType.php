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

namespace CoreShop\Bundle\PayumPaymentBundle\Form\Type;

use CoreShop\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use CoreShop\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Payum\Core\Model\GatewayConfigInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class GatewayConfigType extends AbstractResourceType
{
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private FormTypeRegistryInterface $gatewayConfigurationTypeRegistry,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('factoryName', TextType::class)
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $gatewayConfig = $event->getData();

                if (!$gatewayConfig instanceof GatewayConfigInterface) {
                    return;
                }

                /** @psalm-suppress DeprecatedMethod */
                if (!$this->gatewayConfigurationTypeRegistry->has('gateway_config', $gatewayConfig->getFactoryName())) {
                    return;
                }

                /** @psalm-suppress DeprecatedMethod */
                $configType = $this->gatewayConfigurationTypeRegistry->get('gateway_config', $gatewayConfig->getFactoryName());

                $event->getForm()->add('config', $configType, [
                    'auto_initialize' => false,
                ]);
            })
        ;
    }
}

<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\PayumPaymentBundle\Form\Extension;

use CoreShop\Bundle\PayumPaymentBundle\Form\Type\GatewayConfigType;
use Payum\Core\Model\GatewayConfigInterface;
use Payum\Core\Security\CryptedInterface;
use Payum\Core\Security\CypherInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CryptedGatewayConfigTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private ?CypherInterface $cypher = null,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (null === $this->cypher) {
            return;
        }

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
                $gatewayConfig = $event->getData();

                /**
                 * @var GatewayConfigInterface $gatewayConfig
                 */
                if (!$gatewayConfig instanceof CryptedInterface) {
                    return;
                }

                $gatewayConfig->decrypt($this->cypher);
                $gatewayConfig->setConfig($gatewayConfig->getConfig());

                $event->setData($gatewayConfig);
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
                $gatewayConfig = $event->getData();

                /**
                 * @var GatewayConfigInterface $gatewayConfig
                 */
                if (!$gatewayConfig instanceof CryptedInterface) {
                    return;
                }

                $gatewayConfig->setConfig($gatewayConfig->getConfig());
                $gatewayConfig->encrypt($this->cypher);

                $event->setData($gatewayConfig);
            })
        ;
    }

    public static function getExtendedTypes(): array
    {
        return [GatewayConfigType::class];
    }
}

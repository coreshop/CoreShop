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

namespace CoreShop\Bundle\CoreBundle\Form\Extension;

use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentProviderType;
use CoreShop\Bundle\PayumBundle\Form\Type\GatewayConfigType;
use CoreShop\Bundle\StoreBundle\Form\Type\StoreChoiceType;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PaymentProviderTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stores', StoreChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('gatewayConfig', GatewayConfigType::class)
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
                $paymentMethod = $event->getData();

                if (!$paymentMethod instanceof PaymentProviderInterface) {
                    return;
                }

                $gatewayConfig = $paymentMethod->getGatewayConfig();
                if (null === $gatewayConfig->getGatewayName()) {
                    $gatewayConfig->setGatewayName($paymentMethod->getIdentifier());
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return PaymentProviderType::class;
    }
}

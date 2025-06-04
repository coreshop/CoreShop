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

namespace CoreShop\Bundle\PayumPaymentBundle\Form\Extension;

use CoreShop\Bundle\PaymentBundle\Form\Type\PaymentProviderChoiceType;
use CoreShop\Component\PayumPayment\Model\PaymentProviderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PaymentProviderChoiceTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_attr' => function (PaymentProviderInterface $val) {
                    // adds a class like attending_yes, attending_no, etc
                    return ['data-factory' => $val->getGatewayConfig()->getFactoryName()];
                },
            ])
        ;
    }

    public static function getExtendedTypes(): array
    {
        return [PaymentProviderChoiceType::class];
    }
}

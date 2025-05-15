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

namespace CoreShop\Component\Order\Payment;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderPaymentInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Model\PaymentSettingsAwareInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\TokenGenerator\UniqueTokenGenerator;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrderPaymentProvider implements OrderPaymentProviderInterface
{
    public function __construct(
        private FactoryInterface $paymentFactory,
        private int $decimalFactor,
        private int $decimalPrecision,
        private TranslatorInterface $translator,
    ) {
    }

    public function provideOrderPayment(OrderInterface $order): PaymentInterface
    {
        $tokenGenerator = new UniqueTokenGenerator(true);
        $uniqueId = $tokenGenerator->generate(15);
        $orderNumber = preg_replace(
            '/[^A-Za-z0-9\-_]/',
            '',
            str_replace(' ', '_', $order->getOrderNumber()),
        ) . '_' . $uniqueId;

        /**
         * @var OrderPaymentInterface $payment
         */
        $payment = $this->paymentFactory->createNew();
        $payment->setNumber($orderNumber);
        $payment->setTotalAmount($order->getPaymentTotal());
        $payment->setPaymentProvider($order->getPaymentProvider());
        $payment->setState(PaymentInterface::STATE_NEW);
        $payment->setDatePayment(new \DateTime());
        $payment->setCurrency($order->getCurrency());
        $payment->setOrder($order);

        if ($order instanceof PaymentSettingsAwareInterface) {
            $payment->setDetails($order->getPaymentSettings() ?? []);
        }

        $description = $this->translator->trans(
            'coreshop.order_payment.total',
            [
                '%items%' => count($order->getItems()),
                '%total%' => round($order->getTotal() / $this->decimalFactor, $this->decimalPrecision),
            ],
        );

        $payment->setDescription($description);
        $payment->setCurrencyCode($payment->getCurrency()->getIsoCode());

        return $payment;
    }
}

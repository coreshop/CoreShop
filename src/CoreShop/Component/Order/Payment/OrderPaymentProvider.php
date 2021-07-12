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

namespace CoreShop\Component\Order\Payment;

use CoreShop\Component\Core\Model\Payment;
use CoreShop\Component\Order\Model\OrderPaymentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Model\PaymentSettingsAwareInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\TokenGenerator\UniqueTokenGenerator;
use Payum\Core\Model\ArrayObject;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrderPaymentProvider implements OrderPaymentProviderInterface
{
    private FactoryInterface $paymentFactory;
    private int $decimalFactor;
    private int $decimalPrecision;
    private TranslatorInterface $translator;

    public function __construct(
        FactoryInterface $paymentFactory,
        int $decimalFactor,
        int $decimalPrecision,
        TranslatorInterface $translator
    )
    {
        $this->paymentFactory = $paymentFactory;
        $this->decimalFactor = $decimalFactor;
        $this->decimalPrecision = $decimalPrecision;
        $this->translator = $translator;
    }

    public function provideOrderPayment(OrderInterface $order): PaymentInterface
    {
        $tokenGenerator = new UniqueTokenGenerator(true);
        $uniqueId = $tokenGenerator->generate(15);
        $orderNumber = preg_replace('/[^A-Za-z0-9\-_]/', '', str_replace(' ', '_', $order->getOrderNumber())) . '_' . $uniqueId;

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

        if ($order instanceof PaymentSettingsAwareInterface) {
            $payment->setDetails(new ArrayObject($order->getPaymentSettings() ?? []));
        }

        if ($payment instanceof OrderPaymentInterface) {
            $payment->setOrder($order);
        }

        $description = $this->translator->trans(
            'coreshop.order_payment.total',
            [
                '%items%' => count($order->getItems()),
                '%total%' => round($order->getTotal() / $this->decimalFactor, $this->decimalPrecision),
            ]
        );

        //payum setters
        if ($payment instanceof Payment) {
            $payment->setCurrencyCode($payment->getCurrency()->getIsoCode());
            $payment->setDescription($description);
        }

        return $payment;
    }
}

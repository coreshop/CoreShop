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

namespace CoreShop\Component\Order\Payment;

use CoreShop\Component\Order\Model\OrderPaymentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Payment\Model\PaymentSettingsAwareInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\TokenGenerator\UniqueTokenGenerator;

class OrderPaymentProvider implements OrderPaymentProviderInterface
{
    private $paymentFactory;
    private $decimalFactor;
    private $decimalPrecision;

    public function __construct(FactoryInterface $paymentFactory, int $decimalFactor, int $decimalPrecision)
    {
        $this->paymentFactory = $paymentFactory;
        $this->decimalFactor = $decimalFactor;
        $this->decimalPrecision = $decimalPrecision;
    }

    /**
     * {@inheritdoc}
     */
    public function provideOrderPayment(OrderInterface $order): PaymentInterface
    {
        $tokenGenerator = new UniqueTokenGenerator(true);
        $uniqueId = $tokenGenerator->generate(15);
        $orderNumber = preg_replace('/[^A-Za-z0-9\-_]/', '', str_replace(' ', '_', $order->getOrderNumber())) . '_' . $uniqueId;

        /**
         * @var PaymentInterface $payment
         */
        $payment = $this->paymentFactory->createNew();
        $payment->setNumber($orderNumber);
        $payment->setPaymentProvider($order->getPaymentProvider());
        $payment->setTotalAmount($order->getTotal());
        $payment->setState(PaymentInterface::STATE_NEW);
        $payment->setDatePayment(new \DateTime());

        if (method_exists($payment, 'setCurrency')) {
            $payment->setCurrency($order->getBaseCurrency());
            $payment->setCurrencyCode($order->getBaseCurrency()->getIsoCode());
        }

        if ($order instanceof PaymentSettingsAwareInterface) {
            $payment->setDetails($order->getPaymentSettings());
        }

        if ($payment instanceof OrderPaymentInterface) {
            $payment->setOrder($order);
        }

        $description = sprintf(
            'Payment contains %s item(s) for a total of %s for currency "%s".',
            count($order->getItems()),
            round($order->getTotal() / $this->decimalFactor, $this->decimalPrecision),
            $payment->getCurrencyCode()
        );

        $payment->setDescription($description);

        return $payment;
    }
}

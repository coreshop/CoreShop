<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Payment;

use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderPaymentInterface;
use CoreShop\Component\Payment\Model\PaymentSettingsAwareInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Resource\TokenGenerator\UniqueTokenGenerator;
use Payum\Core\Model\Payment;

class OrderPaymentProvider implements OrderPaymentProviderInterface
{
    /**
     * @var FactoryInterface
     */
    private $paymentFactory;

    /**
     * @var int
     */
    private $decimalFactor;

    /**
     * @var int
     */
    private $decimalPrecision;

    /**
     * @param FactoryInterface $paymentFactory
     * @param int              $decimalFactor
     * @param int              $decimalPrecision
     */
    public function __construct(FactoryInterface $paymentFactory, int $decimalFactor, int $decimalPrecision)
    {
        $this->paymentFactory = $paymentFactory;
        $this->decimalFactor = $decimalFactor;
        $this->decimalPrecision = $decimalPrecision;
    }

    /**
     * {@inheritdoc}
     */
    public function provideOrderPayment(OrderInterface $order)
    {
        $tokenGenerator = new UniqueTokenGenerator(true);
        $uniqueId = $tokenGenerator->generate(15);
        $orderNumber = preg_replace('/[^A-Za-z0-9\-_]/', '', str_replace(' ', '_', $order->getOrderNumber())) . '_' . $uniqueId;

        /**
         * @var PaymentInterface $payment
         * @var Payment          $payment
         */
        $payment = $this->paymentFactory->createNew();
        $payment->setNumber($orderNumber);
        $payment->setPaymentProvider($order->getPaymentProvider());

        // always send desired decimal precision rounded amount to gateway!
        if ($this->decimalFactor === 100) {
            $totalAmount = $order->getTotal();
        } else {
            $totalAmount = (int) round((round($order->getTotal() / $this->decimalFactor, $this->decimalPrecision) * $this->decimalFactor), 0);
        }

        $payment->setTotalAmount($totalAmount);

        $payment->setState(PaymentInterface::STATE_NEW);
        $payment->setDatePayment(new \DateTime());
        $payment->setCurrency($order->getCurrency());

        if ($order instanceof PaymentSettingsAwareInterface) {
            $payment->setDetails($order->getPaymentSettings());
        }

        if ($payment instanceof OrderPaymentInterface) {
            $payment->setOrder($order);
        }

        $description = sprintf(
            'Payment contains %s item(s) for a total of %s.',
            count($order->getItems()),
            round($order->getTotal() / $this->decimalFactor, $this->decimalPrecision)
        );

        //payum setters
        $payment->setCurrencyCode($payment->getCurrency()->getIsoCode());
        $payment->setDescription($description);

        return $payment;
    }
}

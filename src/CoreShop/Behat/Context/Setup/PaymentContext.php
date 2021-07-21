<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Carbon\Carbon;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use CoreShop\Component\PayumPayment\Model\GatewayConfig;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pimcore\Tool;

final class PaymentContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private EntityManagerInterface $entityManager;
    private FactoryInterface $paymentFactory;
    private FactoryInterface $paymentProviderFactory;
    private FactoryInterface $gatewayConfigFactory;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        EntityManagerInterface $entityManager,
        FactoryInterface $paymentFactory,
        FactoryInterface $paymentProviderFactory,
        FactoryInterface $gatewayConfigFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->entityManager = $entityManager;
        $this->paymentFactory = $paymentFactory;
        $this->paymentProviderFactory = $paymentProviderFactory;
        $this->gatewayConfigFactory = $gatewayConfigFactory;
    }

    /**
     * @Given /^There is a payment provider "([^"]+)" using factory "([^"]+)"$/
     * @Given /^the site has a payment provider "([^"]+)" using factory "([^"]+)"$/
     */
    public function thereIsAPaymentProviderUsingFactory($name, $factory)
    {
        /**
         * @var PaymentProviderInterface $paymentProvider
         */
        $paymentProvider = $this->paymentProviderFactory->createNew();
        /**
         * @var GatewayConfig $gatewayConfig
         */
        $gatewayConfig = $this->gatewayConfigFactory->createNew();

        foreach (Tool::getValidLanguages() as $lang) {
            $paymentProvider->setTitle($name, $lang);
        }

        $gatewayConfig->setFactoryName($factory);
        $gatewayConfig->setGatewayName($name);
        $paymentProvider->setGatewayConfig($gatewayConfig);
        $paymentProvider->setIdentifier($name);
        $paymentProvider->addStore($this->sharedStorage->get('store'));
        $paymentProvider->setActive(true);

        $this->entityManager->persist($gatewayConfig);
        $this->entityManager->persist($paymentProvider);
        $this->entityManager->flush();

        $this->sharedStorage->set('payment-provider', $paymentProvider);
    }

    /**
     * @Given /^I create a payment for (my order) with (payment provider "[^"]+") and amount ([^"]+)$/
     * @Given /^I create a payment for (my order) with (payment provider "[^"]+")$/
     */
    public function iCreateAPaymentForOrderWithProviderAndAmount(OrderInterface $order, PaymentProviderInterface $paymentProvider, $amount = null)
    {
        /**
         * @var PaymentInterface $payment
         */
        $payment = $this->paymentFactory->createNew();
        $payment->setCurrency($order->getBaseCurrency());
        $payment->setNumber($order->getId());
        $payment->setPaymentProvider($paymentProvider);
        $payment->setTotalAmount($amount ?? $order->getPaymentTotal());
        $payment->setState(PaymentInterface::STATE_NEW);
        $payment->setDatePayment(Carbon::now());
        $payment->setOrder($order);

        $this->entityManager->persist($payment->getCurrency());
        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        $this->sharedStorage->set('orderPayment', $payment);
    }
}

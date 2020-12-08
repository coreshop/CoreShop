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

namespace CoreShop\Bundle\PayumBundle\Controller;

use CoreShop\Bundle\PayumBundle\Factory\ConfirmOrderFactoryInterface;
use CoreShop\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use CoreShop\Bundle\PayumBundle\Factory\ResolveNextRouteFactoryInterface;
use CoreShop\Component\Core\Model\PaymentProviderInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Payment\OrderPaymentProviderInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Payum\Core\Model\GatewayConfigInterface;
use Payum\Core\Payum;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetStatusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentController extends AbstractController
{
    /**
     * @var OrderPaymentProviderInterface
     */
    private $orderPaymentProvider;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var GetStatusFactoryInterface
     */
    private $getStatusRequestFactory;

    /**
     * @var ResolveNextRouteFactoryInterface
     */
    private $resolveNextRouteRequestFactory;

    /**
     * @var ConfirmOrderFactoryInterface
     */
    private $confirmOrderFactory;

    /**
     * @param OrderPaymentProviderInterface    $orderPaymentProvider
     * @param PimcoreRepositoryInterface       $orderRepository
     * @param GetStatusFactoryInterface        $getStatusRequestFactory
     * @param ResolveNextRouteFactoryInterface $resolveNextRouteRequestFactory
     * @param ConfirmOrderFactoryInterface     $confirmOrderFactory
     */
    public function __construct(
        OrderPaymentProviderInterface $orderPaymentProvider,
        PimcoreRepositoryInterface $orderRepository,
        GetStatusFactoryInterface $getStatusRequestFactory,
        ResolveNextRouteFactoryInterface $resolveNextRouteRequestFactory,
        ConfirmOrderFactoryInterface $confirmOrderFactory
    ) {
        $this->orderPaymentProvider = $orderPaymentProvider;
        $this->orderRepository = $orderRepository;
        $this->getStatusRequestFactory = $getStatusRequestFactory;
        $this->resolveNextRouteRequestFactory = $resolveNextRouteRequestFactory;
        $this->confirmOrderFactory = $confirmOrderFactory;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function prepareCaptureAction(Request $request)
    {
        /**
         * @var $order OrderInterface
         */
        if ($request->attributes->has('token')) {
            $property = 'token';
            $identifier = $request->attributes->get('token');
        } else {
            $property = 'o_id';
            $identifier = $request->get('order');
        }

        /**
         * @var OrderInterface $order
         */
        $order = $this->orderRepository->findOneBy([$property => $identifier]);

        if (null === $order) {
            throw new NotFoundHttpException(sprintf('Order with %s "%s" does not exist.', $property, $identifier));
        }

        $payment = $this->orderPaymentProvider->provideOrderPayment($order);

        $request->getSession()->set('coreshop_order_id', $order->getId());

        $storage = $this->getPayum()->getStorage($payment);
        $storage->update($payment);

        $token = $this->provideTokenBasedOnPayment($payment);

        return $this->redirect($token->getTargetUrl());
    }

    /**
     * Here we return from the Payment Provider and process the result.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     */
    public function afterCaptureAction(Request $request)
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);

        /** @var Generic|GetStatusInterface $status */
        $status = $this->getStatusRequestFactory->createNewWithModel($token);
        $this->getPayum()->getGateway($token->getGatewayName())->execute($status);

        $confirmOrderRequest = $this->confirmOrderFactory->createNewWithModel($status->getFirstModel());
        $this->getPayum()->getGateway($token->getGatewayName())->execute($confirmOrderRequest);

        $resolveNextRoute = $this->resolveNextRouteRequestFactory->createNewWithModel($status->getFirstModel());
        $this->getPayum()->getGateway($token->getGatewayName())->execute($resolveNextRoute);
        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);

        return $this->redirectToRoute($resolveNextRoute->getRouteName(), $resolveNextRoute->getRouteParameters());
    }

    /**
     * @return Payum
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return mixed
     */
    private function provideTokenBasedOnPayment(PaymentInterface $payment)
    {
        /** @var PaymentProviderInterface $paymentMethod */
        $paymentMethod = $payment->getPaymentProvider();

        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();

        if (isset($gatewayConfig->getConfig()['use_authorize']) && $gatewayConfig->getConfig()['use_authorize'] === true) {
            $token = $this->getPayum()->getTokenFactory()->createAuthorizeToken(
                $gatewayConfig->getGatewayName(),
                $payment,
                'coreshop_payment_after',
                []
            );
        } else {
            $token = $this->getPayum()->getTokenFactory()->createCaptureToken(
                $gatewayConfig->getGatewayName(),
                $payment,
                'coreshop_payment_after',
                []
            );
        }

        return $token;
    }
}

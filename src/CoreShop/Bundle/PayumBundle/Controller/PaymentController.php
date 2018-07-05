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

namespace CoreShop\Bundle\PayumBundle\Controller;

use CoreShop\Bundle\PayumBundle\Request\ConfirmOrder;
use CoreShop\Bundle\PayumBundle\Request\GetStatus;
use CoreShop\Bundle\PayumBundle\Request\ResolveNextRoute;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Payment\OrderPaymentProviderInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Payum\Core\Payum;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentController extends Controller
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
     * @var ObjectServiceInterface
     */
    private $pimcoreObjectService;

    /**
     * PaymentController constructor.
     *
     * @param OrderPaymentProviderInterface $orderPaymentProvider,
     * @param PimcoreRepositoryInterface    $orderRepository
     * @param ObjectServiceInterface        $pimcoreObjectService
     */
    public function __construct(
        OrderPaymentProviderInterface $orderPaymentProvider,
        PimcoreRepositoryInterface $orderRepository,
        ObjectServiceInterface $pimcoreObjectService
    ) {
        $this->orderPaymentProvider = $orderPaymentProvider;
        $this->orderRepository = $orderRepository;
        $this->pimcoreObjectService = $pimcoreObjectService;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function prepareCaptureAction(Request $request)
    {
        /*
         * @var $order OrderInterface
         */
        if ($request->attributes->has('token')) {
            $property = 'token';
            $identifier = $request->attributes->get('token');
        } else {
            $property = 'o_id';
            $identifier = $request->get('order');
        }

        $order = $this->orderRepository->findOneBy([$property => $identifier]);

        if (null === $order) {
            throw new NotFoundHttpException(sprintf('Order with %s "%s" does not exist.', $property, $identifier));
        }

        $payment = $this->orderPaymentProvider->provideOrderPayment($order);

        $request->getSession()->set('coreshop_order_id', $order->getId());

        $storage = $this->getPayum()->getStorage($payment);
        $storage->update($payment);

        $captureToken = $this->getPayum()->getTokenFactory()->createCaptureToken(
            $payment->getPaymentProvider()->getGatewayConfig()->getGatewayName(),
            $payment,
            'coreshop_payment_after',
            []
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * Here we return from the Payment Provider and process the result.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     * @throws \Payum\Core\Reply\ReplyInterface
     */
    public function afterCaptureAction(Request $request)
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);

        $status = new GetStatus($token);
        $this->getPayum()->getGateway($token->getGatewayName())->execute($status);

        $confirmOrderRequest = new ConfirmOrder($status->getFirstModel());
        $this->getPayum()->getGateway($token->getGatewayName())->execute($confirmOrderRequest);

        $resolveNextRoute = new ResolveNextRoute($status->getFirstModel());
        $this->getPayum()->getGateway($token->getGatewayName())->execute($resolveNextRoute);
        $this->getPayum()->getHttpRequestVerifier()->invalidate($token);

        //if (PaymentInterface::STATE_NEW !== $status->getValue()) {
        //    $request->getSession()->getBag('flashes')->add('info', sprintf('payment.%s', $status->getValue()));
        //}

        //Start Workflow with $status->getStatus()

        /*
         * Further process the status here, kick-off the pimcore workflow for orders?
         */
        return $this->redirectToRoute($resolveNextRoute->getRouteName(), $resolveNextRoute->getRouteParameters());
    }

    /**
     * @return Payum
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }
}

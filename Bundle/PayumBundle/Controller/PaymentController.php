<?php

namespace CoreShop\Bundle\PayumBundle\Controller;

use CoreShop\Component\Core\Pimcore\ObjectServiceInterface;
use CoreShop\Component\Currency\Context\CurrencyContextInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Payment\Model\PaymentInterface;
use CoreShop\Component\Resource\Factory\PimcoreFactoryInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use CoreShop\Component\Resource\Repository\PimcoreRepositoryInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\DetailsAggregateInterface;
use Payum\Core\Payum;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Sync;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentController extends Controller
{
    /**
     * @var PimcoreFactoryInterface
     */
    private $paymentFactory;

    /**
     * @var PimcoreRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var string
     */
    private $paymentPath;

    /**
     * @var ObjectServiceInterface
     */
    private $pimcoreObjectService;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @param PimcoreFactoryInterface $paymentFactory
     * @param PimcoreRepositoryInterface $orderFactory
     * @param $paymentPath
     * @param ObjectServiceInterface $pimcoreObjectService
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(PimcoreFactoryInterface $paymentFactory, PimcoreRepositoryInterface $orderRepository, $paymentPath, ObjectServiceInterface $pimcoreObjectService, CurrencyContextInterface $currencyContext)
    {
        $this->paymentFactory = $paymentFactory;
        $this->orderRepository = $orderRepository;
        $this->paymentPath = $paymentPath;
        $this->pimcoreObjectService = $pimcoreObjectService;
        $this->currencyContext = $currencyContext;
    }

    public function prepareCaptureAction(Request $request, $orderId) {
        /**
         * @var $order OrderInterface
         */
        $order = $this->orderRepository->find($orderId);

        if (null === $order) {
            throw new NotFoundHttpException(sprintf('Order with id "%s" does not exist.', $orderId));
        }
        /**
         * We now have our Order -> So lets to Payment -> Yeah :)
         */

        /**
         * @var $payment PaymentInterface|PimcoreModelInterface
         */
        $payment = $this->paymentFactory->createNew();
        $payment->setParent($this->pimcoreObjectService->createFolderByPath($order->getFullPath() . "/" . $this->paymentPath));
        $payment->setKey(uniqid("payment"));
        $payment->setPaymentProvider($this->getCart()->getPaymentProvider());
        $payment->setCurrency($this->currencyContext->getCurrency());
        $payment->setAmount($order->getTotal());
        $payment->save(); //Not sure if we need a save here, the storage would do it anyway for us, but just to be save

        $request->getSession()->set('coreshop_order_id', $order->getId());

        $storage = $this->getPayum()->getStorage($payment);
        $storage->update($payment);

        $captureToken = $this->getPayum()->getTokenFactory()->createCaptureToken(
            $payment->getPaymentProvider()->getPaymentProviderConfig()->getGatewayName(),
            $payment,
            'coreshop_shop_payment_after_pay',
            []
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * Here we return from the Payment Provider and process the result.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function afterCaptureAction(Request $request)
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);

        $gateway = $this->getPayum()->getGateway($token->getGatewayName());

        try {
            $gateway->execute(new Sync($token));
        } catch (RequestNotSupportedException $e) {}

        $gateway->execute($status = new GetHumanStatus($token));

        return $this->redirectToRoute('coreshop_shop_order_thank_you');
    }

    /**
     * @return \CoreShop\Component\Order\Model\CartInterface
     */
    private function getCart()
    {
        return $this->getCartManager()->getCart();
    }

    /**
     * @return \CoreShop\Bundle\OrderBundle\Manager\CartManager
     */
    private function getCartManager()
    {
        return $this->get('coreshop.cart.manager');
    }

    /**
     * @return Payum
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }
}

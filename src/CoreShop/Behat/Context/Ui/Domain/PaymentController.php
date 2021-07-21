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

namespace CoreShop\Behat\Context\Ui\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Bundle\WorkflowBundle\StateManager\WorkflowStateInfoManagerInterface;
use CoreShop\Component\Core\Model\PaymentInterface;
use CoreShop\Component\Core\Model\PaymentProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Payum\Core\Payum;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class PaymentController implements Context
{
    private SharedStorageInterface $sharedStorage;
    private Payum $payum;
    private RouterInterface $router;
    private WorkflowStateInfoManagerInterface $workflowStateInfoManager;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        Payum $payum,
        RouterInterface $router,
        WorkflowStateInfoManagerInterface $workflowStateInfoManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->payum = $payum;
        $this->router = $router;
        $this->workflowStateInfoManager = $workflowStateInfoManager;
    }

    /**
     * @Then /^I simulate the concurrent requests for notify and capture for the (latest order payment)$/
     */
    public function iOpenCartSummaryPage(PaymentInterface $payment)
    {
        $context = RequestContext::fromUri(getenv('PANTHER_EXTERNAL_BASE_URI'));
        $originalContext = $this->router->getContext();

        $this->router->setContext($context);

        $paymentProvider = $payment->getPaymentProvider();

        Assert::isInstanceOf($paymentProvider, PaymentProvider::class);

        $gateway = $paymentProvider->getGatewayConfig()->getGatewayName();

        $token = $this->payum->getTokenFactory()->createCaptureToken($gateway, $payment, 'coreshop_payment_after');
        $notifyToken = $this->payum->getTokenFactory()->createNotifyToken($gateway, $payment);

        $client = new Client();
        $requests = [
            'notify' => new Request('GET', $notifyToken->getTargetUrl()),
            'capture' => new Request('GET', $token->getTargetUrl(), ['allow_redirects' => true]),
        ];

        $promises = [
            'notify' => $client->sendAsync($requests['notify']),
            'capture' => $client->sendAsync($requests['capture']),
        ];

        $responses = Utils::unwrap($promises);

        /**
         * @var Response $result
         */
        foreach ($responses as $index => $result) {
            Assert::eq(
                $result->getStatusCode(),
                200,
                sprintf(
                    'Status Code should be 200, but a %s found for request %s', $result->getStatusCode(),
                    $requests[$index]->getUri()
                )
            );
        }

        /**
         * @var Concrete $order
         */
        $order = $payment->getOrder();

        $checkStates = $this->workflowStateInfoManager->getStateHistory($order);

        Assert::count(
            $checkStates,
            4,
            sprintf(
                'Expected to have 4 state changes, but got %s instead. Maybe concurrent state changes?',
                count($checkStates)
            )
        );
    }
}

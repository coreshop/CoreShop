<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\OrderMailProcessorSpy;
use CoreShop\Bundle\TestBundle\Service\SharedStorageInterface;
use CoreShop\Component\Core\Notification\Rule\Action\Order\OrderMailActionProcessor;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\Model\Document\Email;
use Webmozart\Assert\Assert;

final class OrderMailContext implements Context
{
    private OrderMailProcessorSpy $orderMailProcessor;

    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private OrderRepositoryInterface $orderRepository,
        private FactoryInterface $notificationRuleFactory,
    ) {
        $this->orderMailProcessor = new OrderMailProcessorSpy();
    }

    /**
     * @When /^the order mail action with the email document as string id is applied to (my order)$/
     */
    public function theOrderMailActionIsAppliedWithStringDocumentId(OrderInterface $order): void
    {
        $emailDocument = $this->sharedStorage->get('email_document');
        Assert::isInstanceOf($emailDocument, Email::class);

        /** @var NotificationRuleInterface $rule */
        $rule = $this->notificationRuleFactory->createNew();

        $processor = new OrderMailActionProcessor($this->orderMailProcessor, $this->orderRepository);
        $processor->apply($order, $rule, [
            'mails' => [$order->getLocaleCode() => (string) $emailDocument->getId()],
            'sendInvoices' => false,
            'sendShipments' => false,
        ]);
    }

    /**
     * @Then /^the email document should have been sent as order mail$/
     */
    public function theEmailDocumentShouldHaveBeenSent(): void
    {
        $emailDocument = $this->sharedStorage->get('email_document');

        Assert::same(
            $this->orderMailProcessor->getSentEmailDocument()?->getId(),
            $emailDocument->getId(),
            'Expected the email document to be sent as order mail.',
        );
    }
}

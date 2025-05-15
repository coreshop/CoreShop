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

namespace CoreShop\Component\Core\Notification\Rule\Action\Order;

use CoreShop\Component\Core\Order\OrderMailProcessorInterface;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Rule\Action\NotificationRuleProcessorInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use Pimcore\Model\Document;

class OrderMailActionProcessor implements NotificationRuleProcessorInterface
{
    public function __construct(
        private OrderMailProcessorInterface $orderMailProcessor,
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function apply($subject, NotificationRuleInterface $rule, array $configuration, array $params = []): void
    {
        if (!array_key_exists('doNotSendToDesignatedRecipient', $configuration)) {
            $configuration['doNotSendToDesignatedRecipient'] = false;
        }

        $params['doNotSendToDesignatedRecipient'] = $configuration['doNotSendToDesignatedRecipient'];
        $order = null;

        if ($subject instanceof OrderInterface) {
            $order = $subject;
        } elseif (isset($params['order_id'])) {
            $order = $this->orderRepository->find($params['order_id']);
        }

        if ($order instanceof OrderInterface) {
            $language = $order->getLocaleCode();

            if (null === $language) {
                throw new \Exception('OrderMailActionProcessor: Language is not set.');
            }

            if (array_key_exists($language, $configuration['mails'])) {
                $mailDocumentId = $configuration['mails'][$language];
                $mailDocument = Document::getById($mailDocumentId);

                $params['mailRule'] = $rule;
                $params['document'] = $subject;

                if ($mailDocument instanceof Document\Email) {
                    $this->orderMailProcessor->sendOrderMail($mailDocument, $order, $configuration['sendInvoices'], $configuration['sendShipments'], $params);
                }
            }
        }
    }
}

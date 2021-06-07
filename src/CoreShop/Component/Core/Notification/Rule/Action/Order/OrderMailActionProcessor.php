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

namespace CoreShop\Component\Core\Notification\Rule\Action\Order;

use CoreShop\Component\Core\Order\OrderMailProcessorInterface;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Rule\Action\NotificationRuleProcessorInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Pimcore\Model\Document;

class OrderMailActionProcessor implements NotificationRuleProcessorInterface
{
    private $orderMailProcessor;

    public function __construct(OrderMailProcessorInterface $orderMailProcessor)
    {
        $this->orderMailProcessor = $orderMailProcessor;
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
        } elseif (array_key_exists('order', $params) && $params['order'] instanceof OrderInterface) {
            $order = $params['order'];
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

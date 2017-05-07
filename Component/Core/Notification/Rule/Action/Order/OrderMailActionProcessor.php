<?php

namespace CoreShop\Component\Core\Notification\Rule\Action\Order;

use CoreShop\Component\Core\Order\OrderMailProcessorInterface;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Rule\Action\NotificationRuleProcessorInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use Pimcore\Model\Document;

class OrderMailActionProcessor implements NotificationRuleProcessorInterface
{
    /**
     * @var OrderMailProcessorInterface
     */
    private $orderMailProcessor;

    /**
     * {@inheritdoc}
     */
    public function apply($subject, NotificationRuleInterface $rule, array $configuration, $params = [])
    {
        $order = null;

        if ($subject instanceof OrderInvoiceInterface) {
            $order = $subject->getOrder();
        } elseif ($subject instanceof OrderInterface) {
            $order = $subject;
        }

        if ($order instanceof OrderInterface) {
            $language = $order->getOrderLanguage();

            if (is_null($language)) {
                throw new \Exception('Language is not set');
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
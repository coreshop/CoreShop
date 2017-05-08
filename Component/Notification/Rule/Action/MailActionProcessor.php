<?php

namespace CoreShop\Component\Notification\Rule\Action;

use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use Pimcore\Mail;
use Pimcore\Model\Document;

class MailActionProcessor implements NotificationRuleProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply($subject, NotificationRuleInterface $rule, array $configuration, $params = [])
    {
        $language = null;
        $mails = $configuration['mails'];

        if (array_key_exists('language', $params)) {
            $language = $params['language'];
        }

        if (is_null($language)) {
            throw new \InvalidArgumentException('Language is not set');
        }

        if (array_key_exists($language, $mails)) {
            $mailDocumentId = $mails[$language];
            $mailDocument = Document::getById($mailDocumentId);
            $recipient = $params['recipient'];

            $params['rule'] = $rule;

            unset($params['recipient'], $params['language']);

            if ($mailDocument instanceof Document\Email) {
                $mail = new Mail();
                $params['object'] = $subject;

                if ($recipient) {
                    $mail->setTo($recipient);
                }

                $mail->setDocument($mailDocument);
                $mail->setParams($params);
                $mail->setEnableLayoutOnPlaceholderRendering(false);

                $mail->send();
            }
        }
    }
}
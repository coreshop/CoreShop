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

namespace CoreShop\Component\Notification\Rule\Action;

use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Pimcore\Mail\MailProcessorInterface;
use Pimcore\Model\Document;

class MailActionProcessor implements NotificationRuleProcessorInterface
{
    public function __construct(
        protected MailProcessorInterface $mailProcessor,
    ) {
    }

    public function apply($subject, NotificationRuleInterface $rule, array $configuration, array $params = []): void
    {
        $language = null;
        $mails = $configuration['mails'];

        if (array_key_exists('_locale', $params)) {
            $language = $params['_locale'];
        }

        if (null === $language) {
            throw new \Exception('MailActionProcessor: Language is not set.');
        }

        if (array_key_exists($language, $mails)) {
            $mailDocumentId = $mails[$language];
            $mailDocument = Document::getById($mailDocumentId);
            $recipient = [];

            if (!$configuration['doNotSendToDesignatedRecipient']) {
                if (array_key_exists('recipient', $params)) {
                    if (is_string($params['recipient'])) {
                        $recipient = [$params['recipient']];
                    } elseif (is_array($params['recipient'])) {
                        $recipient = $params['recipient'];
                    }
                }
            }

            $params['rule'] = $rule;

            if ($mailDocument instanceof Document\Email) {
                $params['object'] = $subject;

                InheritanceHelper::useInheritedValues(function () use ($mailDocument, $subject, $recipient, $params) {
                    $this->mailProcessor->sendMail($mailDocument, $subject, $recipient, [], $params);
                });
            }
        }
    }
}

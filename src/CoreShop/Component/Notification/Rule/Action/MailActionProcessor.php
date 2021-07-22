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

namespace CoreShop\Component\Notification\Rule\Action;

use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Pimcore\Mail\MailProcessorInterface;
use Pimcore\Model\Document;

class MailActionProcessor implements NotificationRuleProcessorInterface
{
    protected MailProcessorInterface $mailProcessor;

    public function __construct(MailProcessorInterface $mailProcessor)
    {
        $this->mailProcessor = $mailProcessor;
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

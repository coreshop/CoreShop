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

namespace CoreShop\Component\Core\Notification\Rule\Action\Order;

use CoreShop\Bundle\ThemeBundle\Service\ThemeHelperInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Rule\Action\MailActionProcessor;
use CoreShop\Component\Notification\Rule\Action\NotificationRuleProcessorInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;

class StoreMailActionProcessor implements NotificationRuleProcessorInterface
{
    public function __construct(
        protected MailActionProcessor $mailActionProcessor,
        protected ThemeHelperInterface $themeHelper,
        protected StoreRepositoryInterface $storeRepository,
    ) {
    }

    public function apply($subject, NotificationRuleInterface $rule, array $configuration, array $params = []): void
    {
        $store = null;
        $mails = $configuration['mails'];

        if ($subject instanceof StoreAwareInterface) {
            $store = $subject->getStore();
        } elseif (isset($params['store_id'])) {
            $store = $this->storeRepository->find($params['store_id']);
        }

        if (!$store instanceof StoreInterface) {
            throw new \Exception('StoreMailActionProcessor: Store is not set.');
        }

        if (array_key_exists($store->getId(), $mails)) {
            $subConfiguration = $configuration;
            $subConfiguration['mails'] = $mails[$store->getId()];

            $this->themeHelper->useTheme(
                $store->getTemplate(),
                function () use ($subject, $rule, $subConfiguration, $params): void {
                    $this->mailActionProcessor->apply($subject, $rule, $subConfiguration, $params);
                },
            );
        }
    }
}

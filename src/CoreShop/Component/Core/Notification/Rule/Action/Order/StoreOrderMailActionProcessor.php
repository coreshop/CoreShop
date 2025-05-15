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

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Rule\Action\NotificationRuleProcessorInterface;
use CoreShop\Component\Store\Model\StoreAwareInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;

class StoreOrderMailActionProcessor implements NotificationRuleProcessorInterface
{
    public function __construct(
        protected OrderMailActionProcessor $orderMailActionProcessor,
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
            $store = $this->storeRepository->find($params['store']);
        }

        if (!$store instanceof StoreInterface) {
            throw new \Exception('StoreOrderMailActionProcessor: Store is not set.');
        }

        if (array_key_exists($store->getId(), $mails)) {
            $subConfiguration = $configuration;
            $subConfiguration['mails'] = $mails[$store->getId()];

            $this->orderMailActionProcessor->apply($subject, $rule, $subConfiguration, $params);
        }
    }
}

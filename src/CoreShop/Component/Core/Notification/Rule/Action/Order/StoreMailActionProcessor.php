<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Notification\Rule\Action\Order;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Rule\Action\MailActionProcessor;
use CoreShop\Component\Notification\Rule\Action\NotificationRuleProcessorInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreAwareInterface;

class StoreMailActionProcessor implements NotificationRuleProcessorInterface
{
    /**
     * @var MailActionProcessor
     */
    protected $mailActionProcessor;

    /**
     * @var StoreContextInterface
     */
    protected $storeContext;

    /**
     * @param MailActionProcessor   $mailActionProcessor
     * @param StoreContextInterface $storeContext
     */
    public function __construct(MailActionProcessor $mailActionProcessor, StoreContextInterface $storeContext)
    {
        $this->mailActionProcessor = $mailActionProcessor;
        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($subject, NotificationRuleInterface $rule, array $configuration, $params = [])
    {
        $language = null;
        $store = null;
        $mails = $configuration['mails'];

        if (array_key_exists('store', $params)) {
            $store = $params['store'];
        }

        if ($subject instanceof StoreAwareInterface) {
            $store = $subject->getStore();
        }

        if (!$store instanceof StoreInterface) {
            try {
                $store = $this->storeContext->getStore();
            } catch (StoreNotFoundException $exception) {
            }
        }

        if (!$store instanceof StoreInterface) {
            throw new \Exception('StoreMailActionProcessor: Store is not set.');
        }

        if (array_key_exists($store->getId(), $mails)) {
            $this->mailActionProcessor->apply($subject, $rule, [
                'mails' => $mails[$store->getId()],
            ], $params);
        }
    }
}

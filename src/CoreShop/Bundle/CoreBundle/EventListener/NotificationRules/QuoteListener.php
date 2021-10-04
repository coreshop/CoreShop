<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\QuoteInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class QuoteListener extends AbstractNotificationRuleListener
{
    public function applyRule(GenericEvent $event)
    {
        $quote = $event->getSubject();

        if (!$quote instanceof QuoteInterface) {
            return;
        }

        $customer = $order->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return;
        }

        $this->rulesProcessor->applyRules('quote', $event->getSubject(), [
            '_locale' => $quote->getLocaleCode(),
            'recipient' => $customer->getEmail(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'quoteNumber' => $quote->getQuoteNumber()
        ]);
    }
}

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

namespace CoreShop\Bundle\CoreBundle\EventListener\NotificationRules;

use CoreShop\Component\Order\Model\OrderInvoiceInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class InvoiceListener extends AbstractNotificationRuleListener
{
    public function applyRule(GenericEvent $event)
    {
        Assert::isInstanceOf($event->getSubject(), OrderInvoiceInterface::class);

        $this->rulesProcessor->applyRules('invoice', $event->getSubject());
    }
}

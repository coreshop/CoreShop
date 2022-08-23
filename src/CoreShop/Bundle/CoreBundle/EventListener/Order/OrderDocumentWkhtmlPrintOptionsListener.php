<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\EventListener\Order;

use CoreShop\Bundle\OrderBundle\Event\WkhtmlOptionsEvent;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;

final class OrderDocumentWkhtmlPrintOptionsListener
{
    public function __construct(private ConfigurationServiceInterface $configurationHelper)
    {
    }

    public function resolveOptions(WkhtmlOptionsEvent $event): void
    {
        $orderDocument = $event->getOrderDocument();

        $event->setOptions($this->configurationHelper->getForStore(sprintf('system.%s.wkhtml', $orderDocument::getDocumentType()), $orderDocument->getOrder()->getStore()));
    }
}

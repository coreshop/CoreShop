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

namespace CoreShop\Bundle\CoreBundle\EventListener\Order;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Pimcore\Print\WkhtmlOptionsEvent;

final class OrderDocumentWkhtmlPrintOptionsListener
{
    public function __construct(
        private ConfigurationServiceInterface $configurationHelper,
    ) {
    }

    public function resolveOptions(WkhtmlOptionsEvent $event): void
    {
        $orderDocument = $event->getOrderDocument();

        $event->setOptions($this->configurationHelper->getForStore(sprintf('system.%s.wkhtml', $orderDocument::getDocumentType()), $orderDocument->getOrder()->getStore()));
    }
}

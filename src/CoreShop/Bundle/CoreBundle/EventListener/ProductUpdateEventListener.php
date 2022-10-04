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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Event\Model\DataObjectEvent;

final class ProductUpdateEventListener
{
    public function __construct(
        private ConfigurationServiceInterface $configurationService,
    ) {
    }

    public function storeConfigurationThatProductChanged(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof PurchasableInterface) {
            return;
        }

        $this->configurationService->set('SYSTEM.PRICE_RULE.UPDATE', time());
    }
}

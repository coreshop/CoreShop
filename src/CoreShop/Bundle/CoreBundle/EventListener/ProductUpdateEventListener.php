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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\EventListener;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use Pimcore\Event\Model\DataObjectEvent;

final class ProductUpdateEventListener
{
    public function __construct(private ConfigurationServiceInterface $configurationService)
    {
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

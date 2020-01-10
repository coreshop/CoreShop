<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\EventListener\Order;

use CoreShop\Bundle\OrderBundle\Event\WkhtmlOptionsEvent;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;

final class OrderDocumentWkhtmlPrintOptionsListener
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationHelper;

    /**
     * @param ConfigurationServiceInterface $configurationHelper
     */
    public function __construct(ConfigurationServiceInterface $configurationHelper)
    {
        $this->configurationHelper = $configurationHelper;
    }

    public function resolveOptions(WkhtmlOptionsEvent $event)
    {
        $orderDocument = $event->getOrderDocument();

        $event->setOptions($this->configurationHelper->getForStore(sprintf('system.%s.wkhtml', $orderDocument::getDocumentType()), $orderDocument->getOrder()->getStore()));
    }
}

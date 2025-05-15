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

namespace CoreShop\Bundle\MoneyBundle\EventListener;

use Pimcore\Bundle\AdminBundle\Event\AdminEvents;
use Pimcore\Bundle\AdminBundle\Event\IndexActionSettingsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IndexActionSettingsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private int $decimalPrecision,
        private int $decimalFactor,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::INDEX_ACTION_SETTINGS => 'onIndexActionSettings',
        ];
    }

    public function onIndexActionSettings(IndexActionSettingsEvent $event): void
    {
        $settings = $event->getSettings();
        $settings['coreshop_money'] = [
            'decimal_precision' => $this->decimalPrecision,
            'decimal_factor' => $this->decimalFactor,
        ];
        $event->setSettings($settings);
    }
}

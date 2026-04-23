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

namespace CoreShop\Bundle\MoneyBundle\AdminClass\EventListener;

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
            /**
             * @phpstan-ignore-next-line
             */
            AdminEvents::INDEX_ACTION_SETTINGS => 'onIndexActionSettings',
        ];
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function onIndexActionSettings(IndexActionSettingsEvent $event): void
    {
        /**
         * @phpstan-ignore-next-line
         */
        $settings = $event->getSettings();
        $settings['coreshop_money'] = [
            'decimal_precision' => $this->decimalPrecision,
            'decimal_factor' => $this->decimalFactor,
        ];
        /**
         * @phpstan-ignore-next-line
         */
        $event->setSettings($settings);
    }
}

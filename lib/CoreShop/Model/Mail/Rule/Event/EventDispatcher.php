<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Mail\Rule\Event;

/**
 * Class EventDispatcher
 * @package CoreShop\Model\Mail\Rule\Condition\Order
 */
class EventDispatcher
{
    public static function getExternalEvents()
    {
        $events = [];

        $result = \Pimcore::getEventManager()->trigger(
            'coreshop.mail.conditions.external-events.add',
            null,
            ['events' => $events]
        );

        if ($result->stopped()) {
            $data = $result->last();
            if (is_array($data)) {
                $events = $data;
            }
        }

        $data = [];

        foreach ($events as $event) {
            $data[] = [
                'name'          => $event['name'],
                'identifier'    => strtoupper(\Pimcore\File::getValidFilename($event['identifier'])),
                'class'         => $event['class']
            ];
        }

        return $data;
    }
}

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

namespace CoreShop\Behat\Service;

final class NotificationRuleListener implements NotificationRuleListenerInterface
{
    /**
     * @var array
     */
    private $firedEvents = [];

    /**
     * {@inheritdoc}
     */
    public function hasBeenFired($type)
    {
        return array_key_exists($type, $this->firedEvents);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->firedEvents = [];
    }

    /**
     * {@inheritdoc}
     */
    public function applyNewFired($type)
    {
        if (!isset($this->firedEvents[$type])) {
            $this->firedEvents[$type] = 0;
        }

        $this->firedEvents[$type]++;
    }
}
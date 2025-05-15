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

namespace CoreShop\Component\Notification\Repository;

use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Rule\Repository\RuleRepositoryInterface;

interface NotificationRuleRepositoryInterface extends RuleRepositoryInterface
{
    /**
     * @param string $type
     *
     * @return NotificationRuleInterface[]
     */
    public function findForType($type): array;
}

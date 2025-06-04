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

namespace CoreShop\Component\Core\Notification\Rule\Condition\Order;

use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use Pimcore\Model\Element\Note;

class CommentChecker extends AbstractConditionChecker
{
    public function isNotificationRuleValid($subject, array $params, array $configuration): bool
    {
        $type = $params['type'] ?? null;
        $comment = $params['comment'] ?? null;
        $submitAsEmail = $params['submitAsEmail'] ?? null;

        $commentAction = $configuration['commentAction'];

        if ($comment instanceof Note) {
            return $submitAsEmail === true && $commentAction === $type;
        }

        return false;
    }
}

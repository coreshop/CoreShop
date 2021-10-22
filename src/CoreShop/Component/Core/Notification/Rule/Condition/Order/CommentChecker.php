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
            return true === $submitAsEmail && $commentAction === $type;
        }

        return false;
    }
}

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

namespace CoreShop\Component\Core\Notification\Rule\Condition;

use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use Webmozart\Assert\Assert;

final class StateTransitionChecker extends AbstractConditionChecker
{
    private string $interface;
    private string $workflowName;

    public function __construct(string $interface, string $workflowName)
    {
        $this->interface = $interface;
        $this->workflowName = $workflowName;
    }

    public function isNotificationRuleValid($subject, array $params, array $configuration): bool
    {
        Assert::isInstanceOf($subject, $this->interface);

        if (isset($params['workflow'])) {
            if ($params['workflow'] !== $this->workflowName) {
                return false;
            }
        }

        if (isset($params['transition'])) {
            return $configuration['transition'] === $params['transition'];
        }

        return false;
    }
}

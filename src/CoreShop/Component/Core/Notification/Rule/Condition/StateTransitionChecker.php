<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Notification\Rule\Condition;

use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use Webmozart\Assert\Assert;

final class StateTransitionChecker extends AbstractConditionChecker
{
    /**
     * @var string
     */
    private $interface;

    /**
     * @var string
     */
    private $workflowName;

    /**
     * @param string $interface
     * @param string $workflowName
     */
    public function __construct(string $interface, string $workflowName)
    {
        $this->interface = $interface;
        $this->workflowName = $workflowName;
    }

    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
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

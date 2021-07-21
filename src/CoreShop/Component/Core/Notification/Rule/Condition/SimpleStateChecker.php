<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Core\Notification\Rule\Condition;

use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;

final class SimpleStateChecker extends AbstractConditionChecker
{
    private string $interface;
    private string $stateField;
    private string $configurationField;

    public function __construct(string $interface, string $stateField, string $configurationField)
    {
        $this->interface = $interface;
        $this->stateField = $stateField;
        $this->configurationField = $configurationField;
    }

    public function isNotificationRuleValid($subject, array $params, array $configuration): bool
    {
        Assert::isInstanceOf($subject, $this->interface);

        $state = PropertyAccess::createPropertyAccessor()->getValue($subject, $this->stateField);

        return $state === $configuration[$this->configurationField];
    }
}

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
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;

final class SimpleStateChecker extends AbstractConditionChecker
{
    /**
     * @psalm-param class-string $interface
     */
    public function __construct(
        /**
         * @psalm-var class-string
         */
        private string $interface,
        private string $stateField,
        private string $configurationField
    ) {
    }

    public function isNotificationRuleValid($subject, array $params, array $configuration): bool
    {
        Assert::isInstanceOf($subject, $this->interface);

        $state = PropertyAccess::createPropertyAccessor()->getValue($subject, $this->stateField);

        return $state === $configuration[$this->configurationField];
    }
}

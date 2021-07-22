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

namespace CoreShop\Component\Core\Notification\Rule\Condition;

use CoreShop\Component\Notification\Rule\Condition\AbstractConditionChecker;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;

final class SimpleStateChecker extends AbstractConditionChecker
{
    /**
     * @var string
     */
    private $interface;

    /**
     * @var string
     */
    private $stateField;

    /**
     * @var string
     */
    private $configurationField;

    /**
     * @param string $interface
     * @param string $stateField
     * @param string $configurationField
     */
    public function __construct(string $interface, string $stateField, string $configurationField)
    {
        $this->interface = $interface;
        $this->stateField = $stateField;
        $this->configurationField = $configurationField;
    }

    /**
     * {@inheritdoc}
     */
    public function isNotificationRuleValid($subject, $params, array $configuration)
    {
        Assert::isInstanceOf($subject, $this->interface);

        $state = PropertyAccess::createPropertyAccessor()->getValue($subject, $this->stateField);

        return $state === $configuration[$this->configurationField];
    }
}

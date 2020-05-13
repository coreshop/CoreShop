<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\RuleBundle\Event;

use CoreShop\Component\Rule\Model\RuleInterface;
use Symfony\Component\EventDispatcher\Event;

final class RuleAvailabilityCheckEvent extends Event
{
    private $rule;

    private $ruleType;
    private $available;

    public function __construct(RuleInterface $rule, string $ruleType, bool $available)
    {
        $this->rule = $rule;
        $this->ruleType = $ruleType;
        $this->available = $available;
    }

    public function getRule(): RuleInterface
    {
        return $this->rule;
    }

    public function getRuleType(): string
    {
        return $this->ruleType;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailability(bool $available): void
    {
        $this->available = $available;
    }
}

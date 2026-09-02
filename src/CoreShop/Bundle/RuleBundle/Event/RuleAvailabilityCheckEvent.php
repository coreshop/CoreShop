<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\RuleBundle\Event;

use CoreShop\Component\Rule\Model\RuleInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class RuleAvailabilityCheckEvent extends Event
{
    public function __construct(
        private RuleInterface $rule,
        private string $ruleType,
        private bool $available,
    ) {
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

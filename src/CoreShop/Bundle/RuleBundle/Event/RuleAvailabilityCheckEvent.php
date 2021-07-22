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

namespace CoreShop\Bundle\RuleBundle\Event;

use CoreShop\Component\Rule\Model\RuleInterface;
use Symfony\Component\EventDispatcher\Event;

final class RuleAvailabilityCheckEvent extends Event
{
    /**
     * @var RuleInterface
     */
    private $rule;

    /**
     * @var string
     */
    private $ruleType;

    /**
     * @var bool
     */
    private $available;

    /**
     * @param RuleInterface $rule
     * @param string        $ruleType
     * @param bool          $available
     */
    public function __construct(RuleInterface $rule, string $ruleType, bool $available)
    {
        $this->rule = $rule;
        $this->ruleType = $ruleType;
        $this->available = $available;
    }

    /**
     * @return RuleInterface
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @return string
     */
    public function getRuleType()
    {
        return $this->ruleType;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->available;
    }

    /**
     * @param bool $available
     */
    public function setAvailability(bool $available)
    {
        $this->available = $available;
    }
}

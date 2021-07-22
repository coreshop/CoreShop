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

namespace CoreShop\Component\Rule\Condition;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Model\ConditionInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

interface RuleConditionsValidationProcessorInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @param ResourceInterface    $subject
     * @param RuleInterface        $rule
     * @param ConditionInterface[] $conditions
     * @param array                $params
     *
     * @return bool
     */
    public function isValid(ResourceInterface $subject, RuleInterface $rule, $conditions, $params = []);

    /**
     * @param ResourceInterface  $subject
     * @param RuleInterface      $rule
     * @param ConditionInterface $condition
     * @param array              $params
     *
     * @return mixed
     */
    public function isConditionValid(ResourceInterface $subject, RuleInterface $rule, ConditionInterface $condition, $params = []);
}

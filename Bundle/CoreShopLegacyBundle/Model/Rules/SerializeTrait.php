<?php

/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Rules;

use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter\Condition\AbstractCondition;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Product\Filter\Similarity\AbstractSimilarity;

/**
 * Trait SerializeTrait
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Rules
 */
trait SerializeTrait
{
    /**
     * serialize rule entries for gui
     *
     * @param AbstractActionCondition[]|AbstractCondition[]|AbstractSimilarity[] $ruleEntries
     *
     * @return array
     */
    public function serializeRuleEntries($ruleEntries) {
        $serialized = [];

        foreach ($ruleEntries as $ruleEntry) {
            $serialized[] = $this->serializeRuleEntry($ruleEntry);
        }

        return $serialized;
    }

    /**
     * Serialize a ActionCondition
     *
     * @param AbstractActionCondition|AbstractCondition|AbstractSimilarity $ruleEntry
     * @return array
     */
    public function serializeRuleEntry($ruleEntry) {
        $conditionVars = get_object_vars($ruleEntry);
        $conditionVars['type'] = $ruleEntry::getType();

        if ($ruleEntry::getType() === "conditions" || $ruleEntry::getType() === "combined") {
            $conditionVars['conditions'] = $this->serializeRuleEntries($conditionVars['conditions']);
        }

        return $conditionVars;
    }
}
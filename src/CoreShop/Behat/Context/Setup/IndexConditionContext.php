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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Index\Condition\CompareCondition;
use CoreShop\Component\Index\Condition\ConcatCondition;
use CoreShop\Component\Index\Condition\GreaterThanCondition;
use CoreShop\Component\Index\Condition\GreaterThanEqualCondition;
use CoreShop\Component\Index\Condition\InCondition;
use CoreShop\Component\Index\Condition\IsNotNullCondition;
use CoreShop\Component\Index\Condition\IsNullCondition;
use CoreShop\Component\Index\Condition\LikeCondition;
use CoreShop\Component\Index\Condition\LowerThanCondition;
use CoreShop\Component\Index\Condition\LowerThanEqualCondition;
use CoreShop\Component\Index\Condition\MatchCondition;
use CoreShop\Component\Index\Condition\NotInCondition;
use CoreShop\Component\Index\Condition\NotLikeCondition;
use CoreShop\Component\Index\Condition\NotMatchCondition;
use CoreShop\Component\Index\Condition\RangeCondition;

final class IndexConditionContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage
    ) {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^there is a compare condition with field-name "([^"]+)" operator "([^"]+)" and value "([^"]+)"$/
     * @Given /^there is a compare condition with field-name "([^"]+)" operator "([^"]+)" and value "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsACompareCondition($fieldName, $operator, $value, $identifier = null)
    {
        $this->addCondition(new CompareCondition($fieldName, $operator, $value), $identifier);
    }

    /**
     * @Given /^there is a match condition with field-name "([^"]+)" and value "([^"]+)"$/
     * @Given /^there is a match condition with field-name "([^"]+)" and value "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsAMatchCondition($fieldName, $value, $identifier = null)
    {
        $this->addCondition(new MatchCondition($fieldName, $value), $identifier);
    }

    /**
     * @Given /^there is a not-match condition with field-name "([^"]+)" and value "([^"]+)"$/
     * @Given /^there is a not-match condition with field-name "([^"]+)" and value "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsANotMatchCondition($fieldName, $value, $identifier = null)
    {
        $this->addCondition(new NotMatchCondition($fieldName, $value), $identifier);
    }

    /**
     * @Given /^there is a greater-than condition with field-name "([^"]+)" and value "([^"]+)"$/
     * @Given /^there is a greater-than condition with field-name "([^"]+)" and value "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsAGreaterThanCondition($fieldName, $value, $identifier = null)
    {
        $this->addCondition(new GreaterThanCondition($fieldName, $value), $identifier);
    }

    /**
     * @Given /^there is a greater-than-equal condition with field-name "([^"]+)" and value "([^"]+)"$/
     * @Given /^there is a greater-than-equal condition with field-name "([^"]+)" and value "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsAGreaterThanEqualCondition($fieldName, $value, $identifier = null)
    {
        $this->addCondition(new GreaterThanEqualCondition($fieldName, $value), $identifier);
    }

    /**
     * @Given /^there is a lower-than condition with field-name "([^"]+)" and value "([^"]+)"$/
     * @Given /^there is a lower-than condition with field-name "([^"]+)" and value "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsALowerThanCondition($fieldName, $value, $identifier = null)
    {
        $this->addCondition(new LowerThanCondition($fieldName, $value), $identifier);
    }

    /**
     * @Given /^there is a lower-than-equal condition with field-name "([^"]+)" and value "([^"]+)"$/
     * @Given /^there is a lower-than-equal condition with field-name "([^"]+)" and value "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsALowerThanEqualCondition($fieldName, $value, $identifier = null)
    {
        $this->addCondition(new LowerThanEqualCondition($fieldName, $value), $identifier);
    }

    /**
     * @Given /^there is a concat condition with field-name "([^"]+)" operator "([^"]+)" and (conditions "[^"]+")$/
     * @Given /^there is a concat condition with field-name "([^"]+)" operator "([^"]+)" and (conditions "[^"]+") with identifier "([^"]+)"$/
     */
    public function thereIsAConcatCondition($fieldName, $operator, array $conditions, $identifier = null)
    {
        $this->addCondition(new ConcatCondition($fieldName, $operator, $conditions), $identifier);
    }

    /**
     * @Given /^there is a in condition with field-name "([^"]+)" and values "([^"]+)"$/
     * @Given /^there is a in condition with field-name "([^"]+)" and values "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsAInCondition($fieldName, $values, $identifier = null)
    {
        $this->addCondition(new InCondition($fieldName, explode(',', $values)), $identifier);
    }

    /**
     * @Given /^there is a not-in condition with field-name "([^"]+)" and values "([^"]+)"$/
     * @Given /^there is a not-in condition with field-name "([^"]+)" and values "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsANotInCondition($fieldName, $values, $identifier = null)
    {
        $this->addCondition(new NotInCondition($fieldName, explode(',', $values)), $identifier);
    }

    /**
     * @Given /^there is a is-null condition with field-name "([^"]+)"$/
     * @Given /^there is a is-null condition with field-name "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsAIsNullCondition($fieldName, $identifier = null)
    {
        $this->addCondition(new IsNullCondition($fieldName), $identifier);
    }

    /**
     * @Given /^there is a is-not-null condition with field-name "([^"]+)"$/
     * @Given /^there is a is-not-null condition with field-name "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsAIsNotNullCondition($fieldName, $identifier = null)
    {
        $this->addCondition(new IsNotNullCondition($fieldName), $identifier);
    }

    /**
     * @Given /^there is a range condition with field-name "([^"]+)" from "([^"]+)" to "([^"]+)"$/
     * @Given /^there is a range condition with field-name "([^"]+)" from "([^"]+)" to "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsARangeCondition($fieldName, $from, $to, $identifier = null)
    {
        $this->addCondition(new RangeCondition($fieldName, $from, $to), $identifier);
    }

    /**
     * @Given /^there is a like condition with field-name "([^"]+)" and pattern "([^"]+)" and value "([^"]+)"$/
     * @Given /^there is a like condition with field-name "([^"]+)" and pattern "([^"]+)" and value "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsALikeCondition($fieldName, $pattern, $value, $identifier = null)
    {
        $this->addCondition(new LikeCondition($fieldName, $pattern, $value), $identifier);
    }

    /**
     * @Given /^there is a not-like condition with field-name "([^"]+)" and pattern "([^"]+)" and value "([^"]+)"$/
     * @Given /^there is a not-like condition with field-name "([^"]+)" and pattern "([^"]+)" and value "([^"]+)" with identifier "([^"]+)"$/
     */
    public function thereIsANotLikeCondition($fieldName, $pattern, $value, $identifier = null)
    {
        $this->addCondition(new NotLikeCondition($fieldName, $pattern, $value), $identifier);
    }

    /**
     * @param string $condition
     * @param null   $identifier
     */
    private function addCondition($condition, $identifier = null)
    {
        $this->sharedStorage->set('index_condition', $condition);

        if ($identifier) {
            $this->sharedStorage->set('index_condition_' . $identifier, $condition);
        }
    }
}

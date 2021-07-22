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

namespace CoreShop\Component\Index\Condition;

class ConcatCondition implements ConditionInterface
{
    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var array
     */
    private $conditions;

    /**
     * @param string $fieldName
     * @param string $operator
     * @param array  $conditions
     */
    public function __construct($fieldName, string $operator, array $conditions)
    {
        $this->fieldName = $fieldName;
        $this->operator = $operator;
        $this->conditions = $conditions;
    }

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     */
    public function setOperator(string $operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param array $conditions
     */
    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;
    }
}

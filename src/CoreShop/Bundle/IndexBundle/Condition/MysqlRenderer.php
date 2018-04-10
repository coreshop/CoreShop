<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\Condition;

use CoreShop\Component\Index\Condition\AbstractRenderer;
use CoreShop\Component\Index\Condition\ConditionInterface;
use Pimcore\Db;

class MysqlRenderer extends AbstractRenderer
{
    /**
     * @var \Pimcore\Db\Connection
     */
    protected $database;

    /**
     * Condition constructor.
     */
    public function __construct()
    {
        $this->database = Db::get();
    }

    /**
     * @param ConditionInterface $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderIn(ConditionInterface $condition, $prefix = null)
    {
        $inValues = [];

        if (is_array($condition->getValues())) {
            foreach ($condition->getValues() as $c => $value) {
                $inValues[] = $this->database->quote($value);
            }
        }

        if (count($inValues) > 0) {
            return '' . $this->quoteFieldName($condition->getFieldName(), $prefix) . ' IN (' . implode(',', $inValues) . ')';
        }

        return '';
    }

    /**
     * @param ConditionInterface $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderIs(ConditionInterface $condition, $prefix = null)
    {
        $value = $condition->getValues();

        return '' . $this->quoteFieldName($condition->getFieldName(), $prefix) . ' IS ' . ($value ? '' : ' NOT ') . 'NULL';
    }

    /**
     * @param ConditionInterface $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderLike(ConditionInterface $condition, $prefix = null)
    {
        $values = $condition->getValues();
        $pattern = $values['pattern'];

        $value = $values['value'];
        $patternValue = '';

        switch ($pattern) {
            case 'left':
                $patternValue = '%' . $value;
                break;
            case 'right':
                $patternValue = $value . '%';
                break;
            case 'both':
                $patternValue = '%' . $value . '%';
                break;
        }

        return '' . $this->quoteFieldName($condition->getFieldName(), $prefix) . ' LIKE ' . $this->database->quote($patternValue);
    }

    /**
     * @param ConditionInterface $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderRange(ConditionInterface $condition, $prefix = null)
    {
        $values = $condition->getValues();

        return '' . $this->quoteFieldName($condition->getFieldName(), $prefix) . ' >= ' . $values['from'] . ' AND ' . $this->quoteIdentifier($condition->getFieldName(), $prefix) . ' <= ' . $values['to'];
    }

    /**
     * @param ConditionInterface $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderConcat(ConditionInterface $condition, $prefix = null)
    {
        $values = $condition->getValues();
        $conditions = [];

        foreach ($values['conditions'] as $cond) {
            $conditions[] = $this->render($cond, $prefix);
        }

        return '(' . implode(' ' . trim($values['operator']) . ' ', $conditions) . ')';
    }

    /**
     * @param ConditionInterface $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderCompare(ConditionInterface $condition, $prefix = null)
    {
        $values = $condition->getValues();
        $value = $values['value'];
        $operator = $values['operator'];

        return '' . $this->quoteFieldName($condition->getFieldName(), $prefix) . ' ' . $operator . ' ' . $this->database->quote($value);
    }

    /**
     * @param $identifier
     * @return string
     */
    protected function quoteIdentifier($identifier)
    {
        return $this->database->quoteIdentifier($identifier);
    }

    /**
     * @param null $prefix
     * @return string
     */
    protected function renderPrefix($prefix = null)
    {
        if (null === $prefix) {
            return '';
        }

        return $prefix . '.';
    }

    /**
     * @param $fieldName
     * @param null $prefix
     * @return string
     */
    protected function quoteFieldName($fieldName, $prefix = null)
    {
        return $this->renderPrefix($prefix) . $this->quoteIdentifier($fieldName);
    }
}
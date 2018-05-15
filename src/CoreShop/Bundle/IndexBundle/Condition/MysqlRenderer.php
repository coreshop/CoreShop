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

use CoreShop\Component\Index\Condition\CompareCondition;
use CoreShop\Component\Index\Condition\ConcatCondition;
use CoreShop\Component\Index\Condition\ConditionInterface;
use CoreShop\Component\Index\Condition\InCondition;
use CoreShop\Component\Index\Condition\IsCondition;
use CoreShop\Component\Index\Condition\LikeCondition;
use CoreShop\Component\Index\Condition\MatchCondition;
use CoreShop\Component\Index\Condition\NotMatchCondition;
use CoreShop\Component\Index\Condition\RangeCondition;
use CoreShop\Component\Index\Condition\RendererInterface;
use Pimcore\Db;

class MysqlRenderer implements RendererInterface
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
     * {@inheritdoc}
     */
    public function render(ConditionInterface $condition, $prefix = null)
    {
        if ($condition instanceof IsCondition) {
            return $this->renderIs($condition, $prefix);
        }
        elseif ($condition instanceof InCondition) {
            return $this->renderIn($condition, $prefix);
        }
        elseif ($condition instanceof LikeCondition) {
            return $this->renderLike($condition, $prefix);
        }
        elseif ($condition instanceof RangeCondition) {
            return $this->renderRange($condition, $prefix);
        }
        elseif ($condition instanceof ConcatCondition) {
            return $this->renderConcat($condition, $prefix);
        }
        elseif ($condition instanceof CompareCondition) {
            return $this->renderCompare($condition, $prefix);
        }

        throw new \InvalidArgumentException(sprintf('Class %s is not implemented in Mysql Condition Renderer', get_class($condition)));
    }

    /**
     * @param InCondition $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderIn(InCondition $condition, $prefix = null)
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
     * @param IsCondition $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderIs(IsCondition $condition, $prefix = null)
    {
        $value = $condition->getValue();

        return '' . $this->quoteFieldName($condition->getFieldName(), $prefix) . ' IS ' . ($value ? '' : ' NOT ') . 'NULL';
    }

    /**
     * @param LikeCondition $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderLike(LikeCondition $condition, $prefix = null)
    {
        $value = $condition->getValue();
        $pattern = $condition->getPattern();

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
     * @param RangeCondition $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderRange(RangeCondition $condition, $prefix = null)
    {
        $from = $condition->getFrom();
        $to = $condition->getTo();

        return '' . $this->quoteFieldName($condition->getFieldName(), $prefix) . ' >= ' . $from . ' AND ' . $this->quoteFieldName($condition->getFieldName(), $prefix) . ' <= ' . $to;
    }

    /**
     * @param ConcatCondition $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderConcat(ConcatCondition $condition, $prefix = null)
    {
        foreach ($condition->getConditions() as $cond) {
            $conditions[] = $this->render($cond, $prefix);
        }

        return '(' . implode(' ' . trim($condition->getOperator()) . ' ', $conditions) . ')';
    }

    /**
     * @param CompareCondition $condition
     * @param string $prefix
     *
     * @return string
     */
    protected function renderCompare(CompareCondition $condition, $prefix = null)
    {
        $value = $condition->getValue();
        $operator = $condition->getOperator();

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
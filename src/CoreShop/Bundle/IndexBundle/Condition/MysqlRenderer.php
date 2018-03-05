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
     *
     * @return string
     */
    protected function renderIn(ConditionInterface $condition)
    {
        $inValues = [];

        if (is_array($condition->getValues())) {
            foreach ($condition->getValues() as $c => $value) {
                $inValues[] = $this->database->quote($value);
            }
        }

        if (count($inValues) > 0) {
            return 'TRIM(`' . $condition->getFieldName() . '`) IN (' . implode(',', $inValues) . ')';
        }

        return '';
    }

    /**
     * @param ConditionInterface $condition
     *
     * @return string
     */
    protected function renderLike(ConditionInterface $condition)
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

        return 'TRIM(`' . $condition->getFieldName() . '`) LIKE ' . $this->database->quote($patternValue);
    }

    /**
     * @param ConditionInterface $condition
     *
     * @return string
     */
    protected function renderRange(ConditionInterface $condition)
    {
        $values = $condition->getValues();

        return 'TRIM(`' . $condition->getFieldName() . '`) >= ' . $values['from'] . ' AND TRIM(`' . $condition->getFieldName() . '`) <= ' . $values['to'];
    }

    /**
     * @param ConditionInterface $condition
     *
     * @return string
     */
    protected function renderConcat(ConditionInterface $condition)
    {
        $values = $condition->getValues();
        $conditions = [];

        foreach ($values['conditions'] as $cond) {
            $conditions[] = $this->render($cond);
        }

        return '(' . implode(' ' . trim($values['operator']) . ' ', $conditions) . ')';
    }

    /**
     * @param ConditionInterface $condition
     *
     * @return string
     */
    protected function renderCompare(ConditionInterface $condition)
    {
        $values = $condition->getValues();
        $value = $values['value'];
        $operator = $values['operator'];

        return 'TRIM(`' . $condition->getFieldName() . '`) ' . $operator . ' ' . $this->database->quote($value);
    }
}

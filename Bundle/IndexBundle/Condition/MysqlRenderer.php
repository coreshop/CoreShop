<?php

namespace CoreShop\Bundle\IndexBundle\Condition;

use CoreShop\Component\Index\Condition\AbstractRenderer;
use CoreShop\Component\Index\Condition\ConditionInterface;
use Pimcore\Db;

class MysqlRenderer extends AbstractRenderer
{
    /**
     * @var \Pimcore\Db\Connection
     */
    protected $db;

    /**
     * Condition constructor.
     */
    public function __construct()
    {
        $this->db = Db::get();
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
                $inValues[] = $this->db->quote($value);
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
                $patternValue = '%'.$value;
                break;
            case 'right':
                $patternValue = $value.'%';
                break;
            case 'both':
                $patternValue = '%'.$value.'%';
                break;
        }

        return 'TRIM(`'.$condition->getFieldName().'`) LIKE '.$this->db->quote($patternValue);
    }

    /**
     * @param ConditionInterface $condition
     *
     * @return string
     */
    protected function renderRange(ConditionInterface $condition)
    {
        $values = $condition->getValues();

        return 'TRIM(`'.$condition->getFieldName().'`) >= '.$values['from'].' AND TRIM(`'.$condition->getFieldName().'`) <= '.$values['to'];
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

        return '('.implode(' '.trim($values['operator']).' ', $conditions).')';
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

        return 'TRIM(`'.$condition->getFieldName().'`) '.$operator.' '.$this->db->quote($value);
    }
}

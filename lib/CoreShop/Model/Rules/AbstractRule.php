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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Rules;

use CoreShop\Model\AbstractModel;

/**
 * Class AbstractRule
 * @package CoreShop\Model\Rules
 */
abstract class AbstractRule extends AbstractModel
{
    /**
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableConditions = array();

    /**
     * possible types of a action.
     *
     * @var array
     */
    public static $availableActions = array();

    /**
     * @var string
     */
    public static $type;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;


    /**
     * @var array
     */
    public $conditions = [];

    /**
     * @var array
     */
    public $actions = [];

    /**
     * Add Condition Type.
     *
     * @param $condition
     */
    public static function addCondition($condition)
    {
        $class = get_called_class();

        if (!in_array($condition, $class::$availableConditions)) {
            $class::$availableConditions[] = $condition;
        }
    }

    /**
     * Add Action Type.
     *
     * @param $action
     */
    public static function addAction($action)
    {
        $class = get_called_class();

        if (!in_array($action, $class::$availableActions)) {
            $class::$availableActions[] = $action;
        }
    }

    /**
     * @return array
     */
    public static function getAvailableConditions()
    {
        $class = get_called_class();

        return $class::$availableConditions;
    }

    /**
     * @return array
     */
    public static function getAvailableActions()
    {
        $class = get_called_class();

        return $class::$availableActions;
    }

    /**
     * @return string
     */
    public static function getType()
    {
        $class = get_called_class();

        return $class::$type;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }
}

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

namespace CoreShop\Model\Rules;

use CoreShop\Composite\Dispatcher;
use CoreShop\Exception;
use CoreShop\Model\AbstractModel;
use CoreShop\Model\Rules\Action\AbstractAction;
use CoreShop\Model\Rules\Condition\AbstractCondition;
use Pimcore\Tool;

/**
 * Class AbstractRule
 * @package CoreShop\Model\Rules
 */
abstract class AbstractRule extends AbstractModel
{
    /**
     * @var Dispatcher[]
     */
    public static $conditionDispatcher = [];

    /**
     * @var Dispatcher[]
     */
    public static $actionDispatcher = [];

    /**
     * @var string
     */
    public static $type;

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
     * @param $actions
     * @return array
     *
     * @throws \CoreShop\Exception
     */
    public function prepareActions($actions)
    {
        $actionInstances = [];

        foreach ($actions as $action) {
            $className = $this->getMyActionDispatcher()->getClassForType($action['type']);

            if($className && Tool::classExists($className)) {
                $instance = new $className();
                $instance->setValues($action);

                $actionInstances[] = $instance;
            } else {
                throw new Exception(sprintf('Action with type %s and class %s not found', $action['type'], $className));
            }
        }

        return $actionInstances;
    }

    /**
     * @param $conditions

     * @return mixed
     * @throws \CoreShop\Exception
     */
    public function prepareConditions($conditions)
    {
        $conditionInstances = [];

        foreach ($conditions as $condition) {
            $className = $this->getMyConditionDispatcher()->getClassForType($condition['type']);

            if ($className && Tool::classExists($className)) {
                if ($condition['type'] === "conditions") {
                    $nestedConditions = static::prepareConditions($condition['conditions']);
                    $condition['conditions'] = $nestedConditions;
                }

                $instance = new $className();
                $instance->setValues($condition);

                $conditionInstances[] = $instance;
            } else {
                throw new Exception(sprintf('Condition with type %s and class %s not found', $condition['type'], $className));
            }
        }

        return $conditionInstances;
    }

    /**
     * @return Dispatcher
     */
    public static function getConditionDispatcher()
    {
        $calledClass = get_called_class();

        if(is_null(self::$conditionDispatcher[$calledClass])) {
            self::$conditionDispatcher[$calledClass] = new Dispatcher(static::getType() . '.condition', AbstractCondition::class);

            static::initConditionDispatcher(self::$conditionDispatcher[$calledClass]);
        }

        return self::$conditionDispatcher[$calledClass];
    }

    /**
     * @return Dispatcher
     */
    public static function getActionDispatcher()
    {
        $calledClass = get_called_class();

        if(is_null(self::$actionDispatcher[$calledClass])) {
            self::$actionDispatcher[$calledClass] = new Dispatcher(static::getType() . '.action', AbstractAction::class);

            static::initActionDispatcher(self::$actionDispatcher[$calledClass]);
        }

        return self::$actionDispatcher[$calledClass];
    }

    /**
     * Init Dispatcher
     *
     * @param $dispatcher
     */
    protected static function initConditionDispatcher(Dispatcher $dispatcher) {}

    /**
     * Init Dispatcher
     *
     * @param $dispatcher
     */
    protected static function initActionDispatcher(Dispatcher $dispatcher) {}

    /**
     * @return string
     */
    public static function getType()
    {
        return static::$type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s)", $this->getName(), $this->getId());
    }

    /*
     * get Condition dispatcher for instanceof of type
     *
     * @return Dispatcher
     */
    public function getMyConditionDispatcher() {
        return static::getConditionDispatcher();
    }

    /**
     * get action dispatcher for instance of type
     *
     * @return Dispatcher
     */
    public function getMyActionDispatcher() {
        return static::getActionDispatcher();
    }

    /**
     * @return array
     */
    public function serialize() {
        $object = $this->getObjectVars();
        $object['conditions'] = [];
        $object['actions'] = [];

        foreach($this->getConditions() as $condition) {
            $conditionVars = get_object_vars($condition);
            $conditionVars['type'] = $condition::getType();

            $object['conditions'][] = $conditionVars;
        }

        foreach($this->getActions() as $action) {
            $actionVars = get_object_vars($action);
            $actionVars['type'] = $action::getType();

            $object['actions'][] = $actionVars;
        }

        return $object;
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
     * @return AbstractCondition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param AbstractCondition[] $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return AbstractAction[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param AbstractAction[] $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }
}

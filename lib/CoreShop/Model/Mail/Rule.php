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

namespace CoreShop\Model\Mail;

use CoreShop\Exception;
use CoreShop\Model\Mail\Rule\Action\AbstractAction;
use CoreShop\Model\Mail\Rule\Condition\AbstractCondition;
use CoreShop\Model\Mail\Rule\Action;
use CoreShop\Composite\Dispatcher;
use CoreShop\Model\Mail\Rule\Condition;
use CoreShop\Model\Rules\AbstractRule;
use Pimcore\Cache;
use Pimcore\Logger;
use Pimcore\Model\AbstractModel;
use Pimcore\Tool;

/**
 * Class Rule
 * @package CoreShop\Model\Rule
 */
class Rule extends AbstractRule
{
    /**
     * Available Email Rule Types
     *
     * @var array
     */
    public static $availableTypes = ['order', 'invoice', 'shipment', 'user', 'messaging'];

    /**
     * @var
     */
    public static $conditionDispatchers = [];

    /**
     * @var
     */
    public static $actionDispatchers = [];

    /**
     * @var string
     */
    public static $type = 'mailRules';

    /**
     * @var string
     */
    public $mailType;

    /**
     * @var int
     */
    public $sort;

    /**
     * @param string $type
     * @param Dispatcher $dispatcher
     */
    protected static function initConditionDispatchers($type, Dispatcher $dispatcher)
    {
        if ($type === 'order') {
            $dispatcher->addType(Condition\Order\Payment::class);
            $dispatcher->addType(Condition\Order\Carriers::class);
            $dispatcher->addType(Condition\Order\OrderState::class);
            $dispatcher->addType(Condition\Order\InvoiceState::class);
            $dispatcher->addType(Condition\Order\ShipmentState::class);
        } elseif ($type === 'invoice') {
            $dispatcher->addType(Condition\Invoice\InvoiceState::class);
        } elseif ($type === 'messaging') {
            $dispatcher->addType(Condition\Messaging\MessageType::class);
        } elseif ($type === 'user') {
            $dispatcher->addType(Condition\User\UserType::class);
        } elseif ($type === 'shipment') {
            $dispatcher->addType(Condition\Shipment\ShipmentState::class);
        }
    }

    /**
     * @param string $type
     * @param Dispatcher $dispatcher
     */
    protected static function initActionDispatchers($type, Dispatcher $dispatcher)
    {
        $dispatcher->addType(Action\Mail::class);

        if ($type === 'order' || $type === 'shipment' || $type === 'invoice') {
            $dispatcher->addType(Action\OrderMail::class);
        }
    }

    /**
     * @throws Exception
     */
    public static function getActionDispatcher()
    {
        throw new Exception("Mail Rules use multiple dispatchers, each for one type");
    }

    /**
     * @throws Exception
     */
    public static function getConditionDispatcher()
    {
        throw new Exception("Mail Rules use multiple dispatchers, each for one type");
    }

    /**
     * @param $type
     *
     * @return Dispatcher
     */
    public static function getConditionDispatcherForType($type)
    {
        if (!array_key_exists($type, self::$conditionDispatchers)) {
            self::$conditionDispatchers[$type] = new Dispatcher('rules.' . self::getType() . ucfirst($type) . '.condition', AbstractCondition::class);

            self::initConditionDispatchers($type, self::$conditionDispatchers[$type]);
        }

        return self::$conditionDispatchers[$type];
    }

    /**
     * @param $type
     *
     * @return Dispatcher
     */
    public static function getActionDispatcherForType($type)
    {
        if (!array_key_exists($type, self::$actionDispatchers)) {
            self::$actionDispatchers[$type] = new Dispatcher('rules.' . self::getType() . ucfirst($type) . '.action', AbstractAction::class);

            self::initActionDispatchers($type, self::$actionDispatchers[$type]);
        }

        return self::$actionDispatchers[$type];
    }

    /**
     * @deprecated will be removed with 1.3
     *
     * @param $condition
     * @param $type
     */
    public static function addConditionForType($condition, $type)
    {
        $class = '\\CoreShop\\Model\\PriceRule\\Condition\\' . ucfirst($condition);

        static::getConditionDispatcherForType($type)->addType($class);
    }

    /**
     * @deprecated will be removed with 1.3
     *
     * @param $action
     * @param $type
     */
    public static function addActionForType($action, $type)
    {
        $class = '\\CoreShop\\Model\\PriceRule\\Action\\' . ucfirst($action);

        static::getActionDispatcherForType($type)->addType($class);
    }

    /**
     * Apply valid Order Rules
     *
     * @param $type
     * @param AbstractModel $object
     * @param array $params
     */
    public static function apply($type, $object, $params = [])
    {
        $list = static::getList();
        $list->setCondition('mailType = ?', [$type]);
        $list->load();

        foreach ($list->getData() as $rule) {
            if ($rule instanceof static) {
                if ($rule->checkValidity($object, $params)) {
                    $rule->applyRule($object, $params);
                }
            }
        }
    }

    /**
     * @return Dispatcher
     */
    public function getMyConditionDispatcher()
    {
        return static::getConditionDispatcherForType($this->getMailType());
    }

    /**
     * @return Dispatcher
     */
    public function getMyActionDispatcher()
    {
        return static::getActionDispatcherForType($this->getMailType());
    }

    /**
     * @param $object
     * @param array $params
     */
    public function applyRule($object, $params = [])
    {
        foreach ($this->getActions() as $action) {
            if ($action instanceof AbstractAction) {
                $action->apply($object, $this, $params);
            }
        }
    }

    /**
     * Check if Email Rule is valid
     *
     * @param AbstractModel $object
     * @param array $params
     *
     * @return bool
     */
    public function checkValidity(AbstractModel $object, $params = [])
    {
        $cacheKey = $this->getValidationCacheKey($object, $params);

        try {
            $valid = \Zend_Registry::get($cacheKey);
            if ($valid === false) {
                throw new Exception('Validation in registry is null');
            }

            return $valid;
        } catch (\Exception $e) {
            try {
                if (Cache::test($cacheKey)) {
                    $valid = Cache::load($cacheKey);

                    \Zend_Registry::set($cacheKey, $valid ? 1 : 0);
                } else {
                    $valid = true;

                    foreach ($this->getConditions() as $condition) {
                        if ($condition instanceof AbstractCondition) {
                            if (!$condition->checkCondition($object, $params, $this)) {
                                $valid = false;
                                break;
                            }
                        }
                    }


                    \Zend_Registry::set($cacheKey, $valid ? 1 : 0);
                    Cache::save($valid ? 1 : 0, $cacheKey, [$cacheKey, 'coreshop_mail_rule']);
                }

                return $valid;
            } catch (\Exception $e) {
                Logger::warning($e->getMessage());
            }
        }

        return false;
    }

    /**
     * get cache key for mail rule validation
     *
     * @param AbstractModel $object
     * @param array $params
     *
     * @return string
     */
    public function getValidationCacheKey(AbstractModel $object, $params = [])
    {
        $paramsKey = md5(serialize($params));
        $objectKey = md5(serialize($object));

        $cacheKey = \CoreShop::getTools()->getFingerprint() . $paramsKey . $objectKey . $this->getId();

        foreach ($this->getConditions() as $condition) {
            if ($condition instanceof AbstractCondition) {
                $cacheKey = $cacheKey . $condition->getCacheKey();
            }
        }

        return md5($cacheKey);
    }

    /**
     * save model to database.
     */
    public function save()
    {
        parent::save();

        Cache::clearTag('coreshop_mail_rule');
    }

    /**
     * @return string
     */
    public function getMailType()
    {
        return $this->mailType;
    }

    /**
     * @param string $mailType
     */
    public function setMailType($mailType)
    {
        $this->mailType = $mailType;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }
}

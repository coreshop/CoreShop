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

namespace CoreShop\Model\Mail;

use CoreShop\Exception;
use CoreShop\Model\Mail\Rule\Action\AbstractAction;
use CoreShop\Model\Mail\Rule\Condition\AbstractCondition;
use CoreShop\Model\Rules\AbstractRule;
use Pimcore\Cache;
use Pimcore\Logger;
use Pimcore\Model\AbstractModel;

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
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableConditions = [
        'payment' => [
            'order'
        ],
        'orderState' => [
            'order'
        ],
        'carriers' => [
            'order'
        ],
        'invoiceState' => [
            'invoice',
            'order'
        ],
        'shipmentState' => [
            'shipment'
        ],
        'userType' => [
            'user'
        ],
        'messageType' => [
            'messaging'
        ]
    ];

    /**
     * possible types of a action.
     *
     * @var array
     */
    public static $availableActions = [
        'mail' => [
            'user',
            'order',
            'messaging',
            'invoice',
            'shipment'
        ]
    ];

    /**
     * @var string
     */
    public $mailType;

    /**
     * Add Condition Type.
     *
     * @param $condition
     *
     * @throws Exception
     */
    public static function addCondition($condition)
    {
        throw new Exception("You need to call addConditionForType");
    }

    /**
     * Add Action Type.
     *
     * @param $action
     *
     * @throws Exception
     */
    public static function addAction($action)
    {
        throw new Exception("You need to call addActionForType");
    }

    /**
     * Add Condition Type.
     *
     * @param $condition
     * @param $type
     */
    public static function addConditionForType($condition, $type)
    {
        if (!in_array($condition, static::$availableConditions)) {
            static::$availableConditions[] = [
                $condition => []
            ];
        }

        if(!in_array($type, static::$availableConditions[$condition])) {
            static::$availableConditions[$condition][] = $type;
        }
    }

    /**
     * Add Action Type.
     *
     * @param $action
     * @param $type
     */
    public static function addActionForType($action, $type)
    {
        if (!in_array($action, static::$availableActions)) {
            static::$availableActions[] = [
                $action => []
            ];
        }

        if(!in_array($type, static::$availableActions[$action])) {
            static::$availableActions[$action][] = $type;
        }
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
        $list->setCondition("mailType = ?", [$type]);
        $list->load();

        foreach($list->getData() as $rule) {
            if($rule instanceof static) {
                if($rule->checkValidity($object, $params)) {
                    $rule->applyRule($object, $params);
                }
            }
        }
    }

    /**
     * @param $object
     * @param array $params
     *
     * @return boolean
     */
    public function applyRule($object, $params = []) {
        foreach($this->getActions() as $action) {
            if($action instanceof AbstractAction) {
                return $action->apply($object, $this, $params);
            }
        }

        return false;
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

        Cache::clearTag("coreshop_mail_rule");
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
}

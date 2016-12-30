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

namespace CoreShop\Model\Mail\Rule\Condition\Order;

use CoreShop\Model;
use CoreShop\Model\Mail\Rule;
use Pimcore\Model\AbstractModel;

/**
 * Class OrderState
 * @package CoreShop\Model\Mail\Rule\Condition\Order
 */
class OrderState extends Rule\Condition\AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'orderState';

    /**
     *
     */
    const TRANSITION_TO = 1;

    /**
     *
     */
    const TRANSITION_FROM = 2;

    /**
     *
     */
    const TRANSITION_ALL = 3;

    /**
     * @var array
     */
    public $states;

    /**
     * @var int
     */
    public $transitionType;

    /**
     * @param AbstractModel $object
     * @param array $params
     * @param Rule $rule
     *
     * @return boolean
     */
    public function checkCondition(AbstractModel $object, $params = [], Rule $rule)
    {
        if($object instanceof Model\Order) {
            $paramsToExist = [
                'fromState',
                'toState'
            ];

            foreach($paramsToExist as $paramToExist) {
                if(!array_key_exists($paramToExist, $params)) {
                    return false;
                }
            }

            $fromState = $params['fromState'];
            $toState = $params['toState'];

            if($this->getTransitionType() === self::TRANSITION_TO) {
                if(in_array($toState, $this->getStates())) {
                    return true;
                }
            }
            else if($this->getTransitionType() === self::TRANSITION_FROM) {
                if(in_array($fromState, $this->getStates())) {
                    return true;
                }
            }
            else if($this->getTransitionType() === self::TRANSITION_ALL) {
                if(in_array($fromState, $this->getStates()) || in_array($toState, $this->getStates())) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * @param array $states
     */
    public function setStates($states)
    {
        $this->states = $states;
    }

    /**
     * @return int
     */
    public function getTransitionType()
    {
        return $this->transitionType;
    }

    /**
     * @param int $transitionType
     */
    public function setTransitionType($transitionType)
    {
        $this->transitionType = $transitionType;
    }
}

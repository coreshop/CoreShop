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
 * Class ExternalEvent
 * @package CoreShop\Model\Mail\Rule\Condition\Order
 */
class ExternalEvent extends Rule\Condition\AbstractCondition
{
    /**
     * @var string
     */
    public $type = 'externalEvent';

    /**
     * @var string
     */
    public $externalEvent;

    /**
     * @param AbstractModel $object
     * @param array         $params
     * @param Rule          $rule
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function checkCondition(AbstractModel $object, $params = [], Rule $rule)
    {
        if ($object instanceof Model\Order) {
            $events = Rule\Event\EventDispatcher::getExternalEvents();
            foreach($events as $event) {
                if ($this->getExternalEvent() === $event['identifier']) {
                    $class = new $event['class'];
                    return call_user_func_array([$class, 'checkCondition'], [$object, $params, $rule]);
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getExternalEvent()
    {
        return $this->externalEvent;
    }

    /**
     * @param string $externalEvent
     */
    public function setExternalEvent($externalEvent)
    {
        $this->externalEvent = $externalEvent;
    }
}

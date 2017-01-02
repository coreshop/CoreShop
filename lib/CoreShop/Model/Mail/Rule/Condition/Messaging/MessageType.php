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

namespace CoreShop\Model\Mail\Rule\Condition\Messaging;

use CoreShop\Model;
use CoreShop\Model\Mail\Rule;
use Pimcore\Model\AbstractModel;

/**
 * Class MessageType
 * @package CoreShop\Model\Mail\Rule\Condition\Messaging
 */
class MessageType extends Rule\Condition\AbstractCondition
{
    /**
     * @var string
     */
    public static $type = 'messageType';

    /**
     *
     */
    const TYPE_CUSTOMER = 'customer';

    /**
     *
     */
    const TYPE_CUSTOMER_REPLY = 'customer-reply';

    /**
     *
     */
    const TYPE_CUSTOMER_CONTACT = 'contact';

    /**
     * @var int
     */
    public $messageType;

    /**
     * @param AbstractModel $object
     * @param array $params
     * @param Rule $rule
     *
     * @return boolean
     */
    public function checkCondition(AbstractModel $object, $params = [], Rule $rule)
    {
        if($object instanceof Model\Messaging\Message) {
            $paramsToExist = [
                'type'
            ];

            foreach($paramsToExist as $paramToExist) {
                if(!array_key_exists($paramToExist, $params)) {
                    return false;
                }
            }

            $type = $params['type'];

            if($this->getMessageType() === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * @param int $messageType
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
    }
}

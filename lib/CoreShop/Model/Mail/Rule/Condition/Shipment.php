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

namespace CoreShop\Model\Mail\Rule\Condition;

use CoreShop\Model;
use CoreShop\Model\Mail\Rule;
use Pimcore\Model\AbstractModel;

/**
 * Class Shipment
 * @package CoreShop\Model\Mail\Rule\Condition
 */
class Shipment extends AbstractCondition
{
    /**
     *
     */
    const SHIPMENT_TYPE_PARTIAL = 1;

    /**
     *
     */
    const SHIPMENT_TYPE_FULL = 2;

    /**
     *
     */
    const SHIPMENT_TYPE_ALL = 3;

    /**
     * @var string
     */
    public $type = 'shipment';

    /**
     * @var int
     */
    public $shipmentType;

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
                'shipment'
            ];

            foreach($paramsToExist as $paramToExist) {
                if(!array_key_exists($paramToExist, $params)) {
                    return false;
                }
            }

            if($this->getShipmentType() === self::SHIPMENT_TYPE_ALL) {
                return true;
            }
            else if($this->getShipmentType() === self::SHIPMENT_TYPE_FULL) {
                if(count($object->getShipAbleItems()) === 0) {
                    return true;
                }
            }
            else if($this->getShipmentType() === self::SHIPMENT_TYPE_PARTIAL) {
                if(count($object->getShipAbleItems()) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getShipmentType()
    {
        return $this->shipmentType;
    }

    /**
     * @param int $shipmentType
     */
    public function setShipmentType($shipmentType)
    {
        $this->shipmentType = $shipmentType;
    }
}

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

namespace CoreShop\Model\Mail\Rule\Condition\Shipment;

use CoreShop\Model;
use CoreShop\Model\Mail\Rule;
use Pimcore\Model\AbstractModel;

/**
 * Class Carriers
 * @package CoreShop\Model\Mail\Rule\Condition\Shipment
 */
class Carriers extends Rule\Condition\Order\Carriers
{
    /**
     * @param AbstractModel $object
     * @param array $params
     * @param Rule $rule
     *
     * @return boolean
     */
    public function checkCondition(AbstractModel $object, $params = [], Rule $rule)
    {
        if ($object instanceof Model\Order\Shipment) {
            if ($object->getCarrier() instanceof Model\Carrier) {
                if (in_array($object->getCarrier()->getId(), $this->getCarriers())) {
                    return true;
                }
            }
        }

        return false;
    }
}

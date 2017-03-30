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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\AbstractModel;
use CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier;

/**
 * Class ShippingRuleGroup
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Model\Carrier
 */
class ShippingRuleGroup extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $carrierId;

    /**
     * @var Carrier|null
     */
    public $carrier;

    /**
     * @var int
     */
    public $priority;

    /**
     * @var int
     */
    public $shippingRuleId;

    /**
     * @var ShippingRule
     */
    public $shippingRule;

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
     * @return int
     */
    public function getCarrierId()
    {
        return $this->carrierId;
    }

    /**
     * @param int $carrierId
     */
    public function setCarrierId($carrierId)
    {
        $this->carrierId = $carrierId;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getShippingRuleId()
    {
        return $this->shippingRuleId;
    }

    /**
     * @param int $shippingRuleId
     */
    public function setShippingRuleId($shippingRuleId)
    {
        $this->shippingRuleId = $shippingRuleId;
    }

    /**
     * @return Carrier|null
     */
    public function getCarrier()
    {
        if (!$this->carrier instanceof Carrier) {
            $this->carrier = Carrier::getById($this->carrierId);
        }

        return $this->carrier;
    }

    /**
     * @param Carrier|null $carrier
     *
     * @throws Exception
     */
    public function setCarrier($carrier)
    {
        if (!$carrier instanceof Carrier) {
            throw new Exception('$carrier must be instance of Carrier');
        }

        $this->carrier = $carrier;
        $this->carrierId = $carrier->getId();
    }

    /**
     * @return ShippingRule
     */
    public function getShippingRule()
    {
        if (!$this->shippingRule instanceof ShippingRule) {
            $this->shippingRule = ShippingRule::getById($this->shippingRuleId);
        }

        return $this->shippingRule;
    }

    /**
     * @param ShippingRule $shippingRule
     *
     * @throws Exception
     */
    public function setShippingRule($shippingRule)
    {
        if (!$shippingRule instanceof ShippingRule) {
            throw new Exception('$shippingRule must be instance of ShippingRule');
        }

        $this->shippingRule = $shippingRule;
        $this->shippingRuleId = $shippingRule->getId();
    }
}

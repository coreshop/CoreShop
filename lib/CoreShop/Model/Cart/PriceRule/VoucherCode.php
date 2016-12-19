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

namespace CoreShop\Model\Cart\PriceRule;

use CoreShop\Exception;
use CoreShop\Model\AbstractModel;
use CoreShop\Model\Cart;
use Pimcore\Date;

/**
 * Class VoucherCode
 * @package CoreShop\Model\Cart\PriceRule
 */
class VoucherCode extends AbstractModel
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var Date
     */
    public $creationDate;

    /**
     * @var boolean
     */
    public $used;

    /**
     * @var int
     */
    public $uses;

    /**
     * @var int
     */
    public $priceRuleId;

    /**
     * @var Cart\PriceRule
     */
    public $priceRule;

    /**
     * @param $code
     * @return VoucherCode|null
     */
    public static function getByCode($code)
    {
        return self::getByField("code", $code);
    }

    /**
     * Increase Voucher Usage
     */
    public function increaseUsage()
    {
        $this->setUses($this->getUses() + 1);
        $this->setUsed(true);
        $this->save();
    }

    /**
     * Decrease Voucher Usage
     */
    public function decreaseUsage()
    {
        $uses = $this->getUses() - 1;

        if ($uses < 0) {
            $uses = 0;
        }

        if ($uses === 0) {
            $this->setUsed(false);
        }

        $this->setUses($uses);
        $this->save();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s)", $this->getCode(), $this->getId());
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return Date
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param Date $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return boolean
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * @param boolean $used
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }

    /**
     * @return int
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * @param int $uses
     */
    public function setUses($uses)
    {
        $this->uses = $uses;
    }

    /**
     * @return int
     */
    public function getPriceRuleId()
    {
        return $this->priceRuleId;
    }

    /**
     * @param int $priceRuleId
     */
    public function setPriceRuleId($priceRuleId)
    {
        $this->priceRuleId = $priceRuleId;
    }

    /**
     * @return Cart\PriceRule
     */
    public function getPriceRule()
    {
        if (!$this->priceRule) {
            $this->priceRule = Cart\PriceRule::getById($this->getPriceRuleId());
        }

        return $this->priceRule;
    }

    /**
     * @param Cart\PriceRule $priceRule
     *
     * @throws Exception
     */
    public function setPriceRule($priceRule)
    {
        if (!$priceRule instanceof Cart\PriceRule) {
            throw new Exception('$priceRule must be instance of Cart\PriceRule');
        }

        $this->priceRule = $priceRule;
        $this->priceRuleId = $priceRule->getId();
    }
}

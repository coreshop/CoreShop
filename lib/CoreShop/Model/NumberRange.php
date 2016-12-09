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

namespace CoreShop\Model;

/**
 * Class NumberRange
 * @package CoreShop\Model
 */
class NumberRange extends AbstractModel
{
    /**
     * @var bool
     */
    protected static $isMultiShopFK = true;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $number;

    /**
     * @var
     */
    public $shopId;

    /**
     * Get NumberRange by type.
     *
     * @param $type
     * @param $shopId
     *
     * @return NumberRange
     */
    public static function getByType($type, $shopId = null)
    {
        if (is_null($shopId)) {
            $shopId = Shop::getShop()->getId();
        }

        $numberRange = parent::getByField('type', $type, $shopId);

        if (!$numberRange) {
            $numberRange = static::create();
            $numberRange->setType($type);
            $numberRange->setNumber(0);
            $numberRange->setShopId($shopId);
            $numberRange->save();
        }

        return $numberRange;
    }

    /**
     * Returns the next number for a type.
     *
     * @param $type
     * @param $shopId
     *
     * @return int
     */
    public static function getNextNumberForType($type, $shopId = null)
    {
        $numberRange = self::getByType($type, $shopId);
        $numberRange->increaseNumber();

        return $numberRange->getNumber();
    }

    /**
     * Increase number for this NumberRange.
     */
    public function increaseNumber()
    {
        ++$this->number;
        $this->save();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @param mixed $shopId
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }
}

<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;


class NumberRange extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $number;

    /**
     * Save NumberRange
     *
     * @return mixed
     */
    public function save() {
        return $this->getDao()->save();
    }

    /**
     * get NumberRange by id
     *
     * @param $id
     * @return Carrier|null
     */
    public static function getById($id) {
        return parent::getById($id);
    }

    /**
     * Get NumberRange by type
     *
     * @param $type
     * @return NumberRange
     */
    public static function getByType($type) {
        $numberRange = parent::getByField("type", $type);

        if(!$numberRange) {
            $numberRange = new self();
            $numberRange->setType($type);
            $numberRange->setNumber(0);
            $numberRange->save();
        }

        return $numberRange;
    }

    /**
     * Returns the next number for a type
     *
     * @param $type
     * @return int
     */
    public static function getNextNumberForType($type) {
        $numberRange = self::getByType($type);
        $numberRange->increaseNumber();

        return $numberRange->getNumber();
    }

    /**
     * Increase number for this NumberRange
     */
    public function increaseNumber() {
        $this->number++;
        $this->save();
    }

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
}
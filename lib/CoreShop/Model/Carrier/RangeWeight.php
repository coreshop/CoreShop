<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

namespace CoreShop\Model\Carrier;

use Pimcore\Model\AbstractModel;
use CoreShop\Tool;

class RangeWeight extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $carrier;

    /**
     * @var float
     */
    public $delimiter1;

    /**
     * @var float
     */
    public $delimiter2;

    public function save() {
        return $this->getResource()->save();
    }

    public static function getById($id) {
        try {
            $obj = new self;
            $obj->getResource()->getById($id);
            return $obj;
        }
        catch(\Exception $ex) {

        }

        return null;
    }

    /**
     * @return float
     */
    public function getDelimiter2()
    {
        return $this->delimiter2;
    }

    /**
     * @param float $delimiter2
     */
    public function setDelimiter2($delimiter2)
    {
        $this->delimiter2 = $delimiter2;
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
     * @return int
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param int $carrier
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * @return float
     */
    public function getDelimiter1()
    {
        return $this->delimiter1;
    }

    /**
     * @param float $delimiter1
     */
    public function setDelimiter1($delimiter1)
    {
        $this->delimiter1 = $delimiter1;
    }
}
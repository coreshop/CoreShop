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

namespace CoreShop\Model\PriceRule;

use Pimcore\Model;

class AbstractPriceRule {

    /**
     * @var string
     */
    public $elementType;

    /**
     * @var string
     */
    public $type;

    public function setValues(array $values) {
        foreach($values as $key=>$value) {

            if($key == "type")
                continue;

            $setter = "set" . ucfirst($key);

            if(method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }

    /**
     * @return string
     */
    public function getElementType()
    {
        return $this->elementType;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}

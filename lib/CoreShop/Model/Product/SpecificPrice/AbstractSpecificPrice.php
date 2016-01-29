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

namespace CoreShop\Model\Product\SpecificPrice;

use Pimcore\Model;

class AbstractSpecificPrice {

    /**
     * @var string
     */
    public $elementType;

    /**
     * @var string
     */
    public $type;

    /**
     * @param array $values
     */
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

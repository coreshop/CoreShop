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

use Pimcore\Model;

class AbstractModel extends Model\AbstractModel
{
    /**
     * @var array
     */
    protected $localizedValues = array();

    /**
     * @var LocalizedFields
     */
    protected $localizedFields;

    public function getLocalizedFields() {
        if(count($this->localizedValues) > 0) {
            if (is_null($this->localizedFields)) {
                $this->localizedFields = new LocalizedFields($this->localizedValues);
                $this->localizedFields->setObject($this);
            }

            return $this->localizedFields;
        }

        return null;
    }

    /**
     * Override setValue function to support localized fields
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function setValue($key, $value) {
        if($this->getLocalizedFields()) {
            $mykey = explode(".", $key); //0 => key, 1 => language

            if(in_array($mykey [0], $this->localizedValues)) {
                $this->getLocalizedFields()->setLocalizedValue($mykey [0], $value, $mykey [1]);

                return $this;
            }
        }

        return parent::setValue($key, $value);
    }
}
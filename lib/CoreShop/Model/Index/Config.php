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

namespace CoreShop\Model\Index;

use CoreShop\Model;

class Config {

    /**
     * @var Model\Index\Config\Column\AbstractColumn[]
     */
    public $columns;

    /**
     * @return Model\Index\Config\Column\AbstractColumn[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param Model\Index\Config\Column\AbstractColumn[] $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key=>$value) {
            if ($key == "type") {
                continue;
            }

            $setter = "set" . ucfirst($key);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }
}